@fab([
    'id' => 'chat-root',
    'position' => 'bottom-right',
    'heading' => '',
    'button' => [
        'icon' => 'chat',
        'size' => 'md',
        'color' => 'primary',
        'text' => 'Chatta',
        'reversePositions' => true
    ]
])
    <div id="chat-panel" class="u-display--flex u-flex-direction--column u-gap-2" style="max-height: 60vh;">
        @typography(['variant' => 'h6', 'classList' => ['c-fab__heading']])
            Chatta med oss
        @endtypography

        <div id="chat-messages" class="u-padding--2" style="overflow-y: auto; overflow-wrap: anywhere;">
            {{-- Chat messages will be appended here --}}
        </div>

        @form([
        'id' => 'chat-form',
        'action' => '#',
        'method' => 'POST',
        'classList' => ['u-display--flex', 'u-flex-direction--column', 'u-gap-2']
        ])
        @field([
            'id' => 'chat-input',
            'type' => 'text',
            'name' => 'text',
            'label' => 'Skriv din fråga här',
            'multiline' => true
        ])
        @endfield
        @button([
            'id' => 'chat-submit',
            'text' => 'Skicka',
            'color' => 'primary',
            'style' => 'filled',
        ])
        @endbutton
        @endform
    </div>
@endfab

<template id="chat-message-template-user">
    @comment([
        'author' => 'Du',
        'text' => 'asdf',
        'is_reply' => true,
        'date' => ''
    ])
    @endcomment
</template>

<template id="chat-message-template-assistant">
    @comment([
        'author' => 'Assistent',
        'text' => 'asdf',
        'is_reply' => false,
        'date' => ''
    ])
    @endcomment
</template>

<script type="text/javascript" defer>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Chat init');

        const chatRoot = document.getElementById('chat-root');

        const apiRoot = wpApiSettings?.root;

        if (!apiRoot) {
            console.error('No api root found - disabling chat');
            chatRoot.style.display = 'none';
            return;
        }

        // TODO: temp hack
        const panel = chatRoot.querySelector('.c-fab__panel');
        panel.style.maxWidth = '500px';
        panel.style.width = '500px';

        let sessionId = null;

        /**
         * Appends a new message to the chat messages container.
         *
         * @param {string} role - The role of the message sender ('user' or 'assistant').
         * @param {string} text - The text content of the message.
         * @returns {HTMLElement} The newly created message element.
         */
        function appendMessage(role, text) {
            // Create a new chat message element
            const template = document.getElementById('chat-message-template-' + role);
            const newMessage = template.content.cloneNode(true);
            const msgEl = newMessage.querySelector('.c-comment__bubble--inner');
            //msgEl.style.whiteSpace = 'pre-wrap';
            msgEl.textContent = text;

            // Append the new message to the chat messages container
            document.getElementById('chat-messages').appendChild(newMessage);

            msgEl.scrollIntoView({
                behavior: 'smooth'
            });

            return msgEl;
        }

        /**
         * Basic markdown-to-HTML converter. Supports links, bold, italics, and line breaks.
         *
         * @param {string} text - The markdown text to render.
         * @returns {string} The rendered HTML string.
         */
        function renderMarkdown(text) {
            return text
                .replace(/&/g, '&amp;').replace(/</g, '&lt;')
                .replace(/\[([^\]]+)\]\((https?:\/\/[^)]+)\)/g,
                    '<a href="$2" target="_blank" rel="noopener">$1</a>')
                .replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>')
                .replace(/\*(.+?)\*/g, '<em>$1</em>')
                .replace(/\n/g, '<br>');
        }

        async function ask() {
            const inputField = document.getElementById('input_chat-input');
            const messageText = inputField.value.trim();

            if (!messageText || messageText.length === 0) {
                return;
            }

            console.log('sessionId', sessionId);

            // Clear the input field
            inputField.value = '';

            // Append the user's message to the chat
            appendMessage('user', messageText);

            //appendMessage('assistant', '...');
            //return;

            const answerEl = appendMessage('assistant', '');

            const submitButton = document.getElementById('chat-submit');
            submitButton.disabled = true;
            const submitButtonOriginalText = submitButton.textContent;
            submitButton.textContent = 'Skickar...';

            const res = await fetch(`${apiRoot}municipio/v1/chat`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    message: messageText,
                    session_id: sessionId
                })
            });


            if (!res.ok) {
                answerEl.textContent = 'Ett fel uppstod. Försök igen senare.';
                return;
            }

            // Stream response
            const reader = res.body.getReader();
            const decoder = new TextDecoder('utf-8');
            let buffer = "";
            let accum = "";

            while (true) {
                const {
                    done,
                    value
                } = await reader.read();
                if (done) break;

                buffer += decoder.decode(value, {
                    stream: true
                });

                let lines = buffer.split('\n');
                buffer = lines.pop();

                let eventType = "";
                for (const line of lines) {
                    if (line.startsWith("event: ")) {
                        eventType = line.slice(7).trim();
                    } else if (line.startsWith("data: ") && line.slice(6).trim()) {
                        try {
                            const data = JSON.parse(line.slice(6));
                            switch (eventType) {
                                case "first_chunk":
                                    sessionId = data.session_id;
                                    submitButton.textContent = 'Skriver...';
                                    break;
                                case "text":
                                    accum += data.answer;
                                    answerEl.innerHTML = renderMarkdown(accum);
                                    answerEl.scrollIntoView({
                                        behavior: 'smooth'
                                    });
                                    submitButton.textContent = 'Skriver...';
                                    break;
                                case "tool_call":
                                    console.log('Tool call:', data);
                                    submitButton.textContent = 'Verktyg används...';
                                    break;
                            }
                        } catch (e) {
                            console.error('Error parsing chat response:', e);
                            /* ignore parse errors */
                        }
                    }
                }
            }

            submitButton.disabled = false;
            submitButton.textContent = submitButtonOriginalText;
        }

        const submitButton = document.getElementById('chat-submit');
        submitButton.addEventListener('click', (event) => {
            event.preventDefault();
            ask().catch(error => {
                console.error('Chat error:', error);
            });
        });

        const inputField = document.getElementById('input_chat-input');
        inputField.addEventListener('keypress', (event) => {
            if (event.key === 'Enter' && !event.shiftKey) {
                event.preventDefault();
                ask().catch(error => {
                    console.error('Chat error:', error);
                });
            }
        });
    });
</script>
