// function publish() {
//     // Use setTimeout to give the editor time to render the button
//     setTimeout(() => {
//       const publishButton = document.querySelector('.editor-post-publish-button');
//       const urlParams = new URLSearchParams(window.location.search);
//       const currentPageId = urlParams.get('post');

//       if (publishButton) {
//         // publishButton.disabled = true;
//         publishButton.addEventListener('click', (e) => {
//           e.preventDefault();
//         });
//       } else {
//         console.error('Publish button not found.');
//       }
//     }, 1000); // You can adjust the delay (in milliseconds) based on your needs
//   }

//   document.addEventListener('DOMContentLoaded', () => {
//     publish();
//   });
  