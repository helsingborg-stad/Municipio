function loadMoreHandler(event) {
	event.preventDefault();

	const parent = event.target.closest('.mod-curator-load-more');

	const embedCode = parent.getAttribute('data-code');
	const itemCount = parseInt(parent.getAttribute('data-item-count'));
	const itemsPerPage = parseInt(parent.getAttribute('data-items-per-page'));
	let itemsLoaded = parseInt(parent.getAttribute('data-items-loaded'));

	const grandParent = parent.closest('.modularity-socialmedia-container');
	const socialMediaBlocks = grandParent.querySelector('.modularity-socialmedia__content');
	const placeholderBlocks = socialMediaBlocks.querySelectorAll('.modularity-socialmedia__item--placeholder');

	placeholderBlocks.forEach((block) => {
		block.classList.remove('u-display--none');
	});
	if (itemsLoaded < itemCount) {
		getFeed(embedCode, itemsPerPage, itemsLoaded)
			.then((response) => {
				if (response.success && response.posts) {
					updateItems(response.posts, parent);
				}
			})
			.catch((error) => {
				console.error(error);
			});
	} else {
		placeholderBlocks.forEach((block) => {
			block.remove();
		});
		parent.innerHTML = curator.noMoreItems;
	}
}

function getFeed(embedCode, limit, offset) {
	const url = curator.ajaxurl;
	const data = new FormData();

	data.append('action', 'mod_curator_get_feed');
	data.append('nonce', curator.nonce);
	data.append('embed-code', embedCode);
	data.append('limit', limit);
	data.append('offset', offset);

	return fetch(url, {
		method: 'POST',
		body: data,
	}).then((response) => {
		if (response.ok) {
			return response.json();
		} else {
			throw new Error('Network response was not ok');
		}
	});
}

function updateItems(posts, button) {
	renderPosts(posts, button);

	const itemsLoaded = parseInt(button.getAttribute('data-items-loaded'));
	const newItemsLoaded = itemsLoaded + posts.length;
	button.setAttribute('data-items-loaded', newItemsLoaded);
}

function renderPosts(posts, button) {
	const url = curator.ajaxurl;
	const data = new FormData();

	data.append('action', 'mod_curator_load_more');
	data.append('nonce', curator.nonce);
	data.append('layout', button.getAttribute('data-layout'));
	data.append('posts', JSON.stringify(posts));

	const closestParent = button.closest('.modularity-socialmedia-container');
	const socialMediaBlocks = closestParent.querySelector('.modularity-socialmedia__content');
	const placeholderBlock = socialMediaBlocks.querySelector('.modularity-socialmedia__item--placeholder');
	const placeholderBlocks = socialMediaBlocks.querySelectorAll('.modularity-socialmedia__item--placeholder');
	const columnClasses = socialMediaBlocks.querySelector('.modularity-socialmedia__item').getAttribute('class');
	data.append('columnClasses', columnClasses);

	fetch(url, {
		method: 'POST',
		body: data,
	})
		.then((response) => {
			if (response.ok) {
				return response.text();
			} else {
				throw new Error('Network response was not ok');
			}
		})
		.then((html) => {
			placeholderBlock.insertAdjacentHTML('beforebegin', html);
		})
		.catch((error) => {
			console.error(error);
		})
		.finally(() => {
			placeholderBlocks.forEach((block) => {
				block.classList.add('u-display--none');
			});
		});
}


document.addEventListener('DOMContentLoaded', () => {

	// Select all elements with class 'mod-curator-load-more' and convert the result to an array
	const loadMoreButtons = Array.from(document.getElementsByClassName('mod-curator-load-more'));

	// Add the event listener to each button in the array
	loadMoreButtons.forEach((button) => button.addEventListener('click', loadMoreHandler));

}); 