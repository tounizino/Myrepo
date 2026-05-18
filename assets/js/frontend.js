document.addEventListener('DOMContentLoaded', () => {
    const commentContainer = document.getElementById('cgd-comments');
    if (!commentContainer) return;

    const postId = commentContainer.dataset.postId;
    const form = document.getElementById('cgd-comment-form');
    const commentList = document.getElementById('cgd-comments-list');
    const loadMoreBtn = document.getElementById('cgd-load-more');
    let page = 1;
    let sortBy = 'newest';

    // Initial Load
    loadComments();

    // Sorting
    document.querySelectorAll('.cgd-sort-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.cgd-sort-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            sortBy = btn.dataset.sort;
            page = 1;
            commentList.innerHTML = '<div class="cgd-loading-skeleton"><div class="cgd-skeleton-item"></div><div class="cgd-skeleton-item"></div></div>';
            loadComments();
        });
    });

    // Form Submission
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalBtnText = submitBtn.innerHTML;
        
        submitBtn.disabled = true;
        submitBtn.innerHTML = cgdData.i18n.posting;

        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());

        try {
            const response = await fetch(`${cgdData.restUrl}/comments`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-WP-Nonce': cgdData.nonce
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (response.ok) {
                // Prepend new comment
                const commentHtml = renderComment(result);
                commentList.insertAdjacentHTML('afterbegin', commentHtml);
                form.reset();
                
                // Scroll to comment
                const newComment = commentList.firstElementChild;
                newComment.scrollIntoView({ behavior: 'smooth' });
            } else {
                alert(result.message || cgdData.i18n.error);
            }
        } catch (error) {
            console.error('Error posting comment:', error);
            alert(cgdData.i18n.error);
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnText;
        }
    });

    async function loadComments() {
        try {
            const response = await fetch(`${cgdData.restUrl}/comments?post=${postId}&page=${page}&sort=${sortBy}`);
            const result = await response.json();

            if (page === 1) {
                commentList.innerHTML = '';
            }

            if (result.comments && result.comments.length > 0) {
                result.comments.forEach(comment => {
                    commentList.insertAdjacentHTML('beforeend', renderComment(comment));
                });

                if (result.hasMore) {
                    loadMoreBtn.style.display = 'block';
                } else {
                    loadMoreBtn.style.display = 'none';
                }
            } else if (page === 1) {
                commentList.innerHTML = '<div class="cgd-empty">No comments yet. Start the discussion!</div>';
            }
        } catch (error) {
            console.error('Error loading comments:', error);
        }
    }

    function renderComment(comment) {
        return `
            <div class="cgd-comment cgd-glass-panel" id="comment-${comment.id}">
                <div class="cgd-comment-header">
                    <img src="${comment.author_avatar_urls['48']}" class="cgd-avatar" alt="${comment.author_name}">
                    <div class="cgd-meta">
                        <span class="cgd-author-name">${comment.author_name}</span>
                        <span class="cgd-time">${comment.date_relative}</span>
                    </div>
                </div>
                <div class="cgd-comment-content">
                    ${comment.content.rendered}
                </div>
                <div class="cgd-reactions">
                    <button class="cgd-reaction-btn" data-type="fire" data-comment-id="${comment.id}">🔥 <span class="count">${comment.reactions?.fire || 0}</span></button>
                    <button class="cgd-reaction-btn" data-type="gaming" data-comment-id="${comment.id}">🎮 <span class="count">${comment.reactions?.gaming || 0}</span></button>
                    <button class="cgd-reaction-btn" data-type="like" data-comment-id="${comment.id}">👍 <span class="count">${comment.reactions?.like || 0}</span></button>
                    <button class="cgd-reaction-btn" data-type="dislike" data-comment-id="${comment.id}">👎 <span class="count">${comment.reactions?.dislike || 0}</span></button>
                </div>
            </div>
        `;
    }

    // Load More
    loadMoreBtn?.addEventListener('click', () => {
        page++;
        loadComments();
    });

    // Handle Reactions
    document.addEventListener('click', async (e) => {
        const reactionBtn = e.target.closest('.cgd-reaction-btn');
        if (!reactionBtn) return;

        const commentId = reactionBtn.dataset.commentId;
        const type = reactionBtn.dataset.type;

        try {
            const response = await fetch(`${cgdData.restUrl}/reactions`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-WP-Nonce': cgdData.nonce
                },
                body: JSON.stringify({ comment_id: commentId, type: type })
            });

            const result = await response.json();
            if (response.ok) {
                const countSpan = reactionBtn.querySelector('.count');
                countSpan.textContent = result.new_count;
                reactionBtn.classList.toggle('active');
            }
        } catch (error) {
            console.error('Error toggling reaction:', error);
        }
    // Reading Progress
    const progressBar = document.createElement('div');
    progressBar.id = 'cgd-reading-progress';
    document.body.appendChild(progressBar);

    window.addEventListener('scroll', () => {
        const winScroll = document.body.scrollTop || document.documentElement.scrollTop;
        const height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
        const scrolled = (winScroll / height) * 100;
        progressBar.style.width = scrolled + "%";
    });

    // Share Bar
    const shareBtns = document.querySelectorAll('.cgd-share-btn');
    shareBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const platform = btn.dataset.platform;
            const url = encodeURIComponent(window.location.href);
            const title = encodeURIComponent(document.title);
            let shareUrl = '';

            switch (platform) {
                case 'x': shareUrl = `https://x.com/intent/tweet?url=${url}&text=${title}`; break;
                case 'facebook': shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${url}`; break;
                case 'reddit': shareUrl = `https://reddit.com/submit?url=${url}&title=${title}`; break;
                case 'whatsapp': shareUrl = `https://api.whatsapp.com/send?text=${title}%20${url}`; break;
                case 'copy':
                    navigator.clipboard.writeText(window.location.href);
                    alert('Link copied!');
                    return;
            }

            if (shareUrl) window.open(shareUrl, '_blank', 'width=600,height=400');
        });
    });

    // Exit Intent
    let exitShown = false;
    document.addEventListener('mouseleave', (e) => {
        if (e.clientY < 0 && !exitShown) {
            document.getElementById('cgd-exit-popup').style.display = 'flex';
            exitShown = true;
        }
    });

    document.querySelector('.cgd-close-popup')?.addEventListener('click', () => {
        document.getElementById('cgd-exit-popup').style.display = 'none';
    });

    document.getElementById('cgd-exit-form')?.addEventListener('submit', async (e) => {
        e.preventDefault();
        const email = e.target.email.value;
        const btn = e.target.querySelector('button');
        btn.disabled = true;

        try {
            const response = await fetch(`${cgdData.restUrl}/subscribe`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-WP-Nonce': cgdData.nonce
                },
                body: JSON.stringify({ email: email })
            });

            if (response.ok) {
                e.target.innerHTML = '<h3>Subscribed! Thanks.</h3>';
                setTimeout(() => {
                    document.getElementById('cgd-exit-popup').style.display = 'none';
                }, 2000);
            }
        } catch (error) {
            console.error('Error subscribing:', error);
            btn.disabled = false;
        }
    });

    // Reveal Spoilers
    document.addEventListener('click', (e) => {
        if (e.target.classList.contains('cgd-spoiler')) {
            e.target.classList.toggle('revealed');
        }
    });

    // Handle Star Ratings
    document.querySelectorAll('.cgd-star').forEach(star => {
        star.addEventListener('click', async () => {
            const rating = star.dataset.value;
            const postId = star.parentElement.dataset.postId;

            try {
                const response = await fetch(`${cgdData.restUrl}/ratings`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-WP-Nonce': cgdData.nonce
                    },
                    body: JSON.stringify({ post_id: postId, rating: rating })
                });

                const result = await response.json();
                if (response.ok) {
                    // Update stars UI
                    const stars = star.parentElement.querySelectorAll('.cgd-star');
                    stars.forEach((s, index) => {
                        if (index < rating) {
                            s.classList.add('active');
                        } else {
                            s.classList.remove('active');
                        }
                    });
                    document.querySelector('.cgd-rating-text').textContent = `Average Rating: ${result.new_avg}`;
                }
            } catch (error) {
                console.error('Error submitting rating:', error);
            }
        });
    });
});
