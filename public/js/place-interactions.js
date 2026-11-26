// Likes and comments without reloading the page
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

// send a request to Laravel with the CSRF token and get JSON back
function sendRequest(url, method, body = null) {
    return fetch(url, {
        method: method,
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
            'Content-Type': 'application/json',
        },
        body: body ? JSON.stringify(body) : null,
    });
}

// like / unlike
const likeButton = document.getElementById('like-button');

if (likeButton) {
    likeButton.addEventListener('click', () => {
        sendRequest(likeButton.dataset.url, 'POST')
            .then(response => response.json())
            .then(data => {
                document.getElementById('like-count').textContent = data.count;
                likeButton.classList.toggle('btn-danger', data.liked);
                likeButton.classList.toggle('btn-outline-danger', !data.liked);
            });
    });
}

// add a comment
const commentForm = document.getElementById('comment-form');

if (commentForm) {
    commentForm.addEventListener('submit', event => {
        event.preventDefault();
        const bodyInput = document.getElementById('comment-body');
        const errorBox = document.getElementById('comment-error');

        sendRequest(commentForm.dataset.url, 'POST', { body: bodyInput.value })
            .then(response => response.json().then(data => ({ ok: response.ok, data })))
            .then(({ ok, data }) => {
                if (!ok) {
                    errorBox.textContent = data.message; // validation error from Laravel
                    return;
                }
                errorBox.textContent = '';
                bodyInput.value = '';
                document.getElementById('comment-list').insertAdjacentHTML('afterbegin', data.html);
                document.getElementById('comment-count').textContent = data.count;
            });
    });
}

// delete a comment (buttons can be added later, so listen on the list)
document.getElementById('comment-list').addEventListener('click', event => {
    if (!event.target.classList.contains('delete-comment')) return;
    if (!confirm('Delete this comment?')) return;

    sendRequest(event.target.dataset.url, 'DELETE')
        .then(response => response.json())
        .then(data => {
            event.target.closest('.comment').remove();
            document.getElementById('comment-count').textContent = data.count;
        });
});
