<ul class="comments" id="comments">
        <li class="comment" id="comment-9999">


            @component('components.user', ['name' => "Lorem ipsum", 'role' => "Systems developer"])
                Hey you!
            @endcomponent

            <div class="author-image">
                <a href="#"><img src="https:unsplash.it/200/200?image=1005"></a>
            </div>
            <div class="comment-body">
                <div class="comment-header">
                    <em class="author-name"><a href="#">Random hero</a></em>
                    @include('utilties.date', ['date' => '2017-08-22 06:36:28']);
                </div>
                <div class="comment-content">
                    <p>Lorem ipsum curabitur donec tempor nullam senectus curabitur taciti quis eget ultrices varius lacinia purus sodales pulvinar, ornare himenaeos per feugiat lacus sagittis venenatis interdum, amet ultricies sodales maecenas nostra porta.</p>
                </div>
                <div class="comment-footer">
                    <span class="like">
                        <a class="like-button" href="#" data-comment-id="9999"><span id="like-count">0</span></a>
                    </span>
                    <span class="reply">
                        <a class="comment-reply-link" href="#">Svara</a>
                    </span>
                </div>
            </div>
            <ul class="answers">
                <li class="answer" id="answer-9999">
                    <div class="author-image">
                        <a href="#"><img src="https:unsplash.it/200/200?image=1005"></a>
                    </div>
                    <div class="comment-body">
                        <div class="comment-header">
                            <em class="author-name"><a href="#">Random hero 2</a></em>
                            <time data-tooltip="2017-08-22 kl. 08:36" data-tooltip-right="" datetime="2017-08-22 06:36:28">20 minuter sedan</time>
                        </div>
                        <div class="comment-content">
                            <p>Lorem ipsum curabitur donec tempor nullam senectus curabitur taciti quis eget ultrices varius lacinia purus sodales pulvinar, ornare himenaeos per feugiat lacus sagittis venenatis interdum, amet ultricies sodales maecenas nostra porta.</p>
                        </div>
                        <div class="comment-footer">
                            <span class="like">
                                <a class="like-button" href="#" data-comment-id="9999"><span id="like-count">0</span></a>
                            </span>
                            <span class="reply">
                                <a class="comment-reply-link" href="#">Svara</a>
                            </span>
                        </div>
                    </div>
                </li>
                <li class="answer" id="answer-9999">
                    <div class="author-image">
                         <a href="#"><img src="https:unsplash.it/200/200?image=1005"></a>
                    </div>
                    <div class="comment-body">
                        <div class="comment-header">
                            <em class="author-name"><a href="#">Random hero 3</a></em>
                            <time data-tooltip="2017-08-22 kl. 08:36" data-tooltip-right="" datetime="2017-08-22 06:36:28">20 minuter sedan</time>
                        </div>
                        <div class="comment-content">
                            <p>Lorem ipsum curabitur donec tempor nullam senectus curabitur taciti quis eget ultrices varius lacinia purus sodales pulvinar, ornare himenaeos per feugiat lacus sagittis venenatis interdum, amet ultricies sodales maecenas nostra porta.</p>
                        </div>
                        <div class="comment-footer">
                            <span class="like">
                                <a class="like-button active" href="#" data-comment-id="9999"><span id="like-count">11</span></a>
                            </span>
                            <span class="reply">
                                <a class="comment-reply-link" href="#">Svara</a>
                            </span>
                        </div>
                    </div>
                </li>
            </ul>
        </li>
        <li class="comment" id="comment-9999">
            <div class="author-image">
                <a href="#"><i class="pricon pricon-2x pricon-user-o"></i></a>
            </div>
            <div class="comment-body">
                <div class="comment-header">
                    <em class="author-name"><a href="#">Random hero</a></em>
                    <time data-tooltip="2017-08-22 kl. 08:36" data-tooltip-right="" datetime="2017-08-22 06:36:28">20 minuter sedan</time>
                </div>
                <div class="comment-content">
                    <p>Lorem ipsum curabitur donec tempor nullam senectus curabitur taciti quis eget ultrices varius lacinia purus sodales pulvinar, ornare himenaeos per feugiat lacus sagittis venenatis interdum, amet ultricies sodales maecenas nostra porta.</p>
                </div>
                <div class="comment-footer">
                    <span class="like">
                        <a class="like-button active" href="#" data-comment-id="9999"><span id="like-count">11</span></a>
                    </span>
                    <span class="reply">
                        <a class="comment-reply-link" href="#">Svara</a>
                    </span>
                </div>
            </div>
            <ul class="answers">
                <li class="answer" id="answer-9999">
                    <div class="author-image">
                        <a href="#"><i class="pricon pricon-2x pricon-user-o"></i></a>
                    </div>
                    <div class="comment-body">
                        <div class="comment-header">
                            <em class="author-name"><a href="#">Random hero 2</a></em>
                            <time data-tooltip="2017-08-22 kl. 08:36" data-tooltip-right="" datetime="2017-08-22 06:36:28">20 minuter sedan</time>
                        </div>
                        <div class="comment-content">
                            <p>Lorem ipsum curabitur donec tempor nullam senectus curabitur taciti quis eget ultrices varius lacinia purus sodales pulvinar, ornare himenaeos per feugiat lacus sagittis venenatis interdum, amet ultricies sodales maecenas nostra porta.</p>
                        </div>
                        <div class="comment-footer">
                            <span class="like">
                                <a class="like-button" href="#" data-comment-id="9999"><span id="like-count">0</span></a>
                            </span>
                            <span class="reply">
                                <a class="comment-reply-link" href="#">Svara</a>
                            </span>
                        </div>
                    </div>
                </li>
                <li class="answer" id="answer-9999">
                    <div class="author-image">
                        <a href="#"><i class="pricon pricon-2x pricon-user-o"></i></a>
                    </div>
                    <div class="comment-body">
                        <div class="comment-header">
                            <em class="author-name"><a href="#">Random hero 3</a></em>
                            <time data-tooltip="2017-08-22 kl. 08:36" data-tooltip-right="" datetime="2017-08-22 06:36:28">20 minuter sedan</time>
                        </div>
                        <div class="comment-content">
                            <p>Lorem ipsum curabitur donec tempor nullam senectus curabitur taciti quis eget ultrices varius lacinia purus sodales pulvinar, ornare himenaeos per feugiat lacus sagittis venenatis interdum, amet ultricies sodales maecenas nostra porta.</p>
                        </div>
                        <div class="comment-footer">
                            <span class="like">
                                <a class="like-button active" href="#" data-comment-id="9999"><span id="like-count">11</span></a>
                            </span>
                            <span class="reply">
                                <a class="comment-reply-link" href="#">Svara</a>
                            </span>
                        </div>
                    </div>
                </li>
            </ul>
        </li>
    </ul>
