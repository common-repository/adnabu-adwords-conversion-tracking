function enable_tracker(id, url, nonce) {
    window.open(url);
    window.addEventListener('focus',function () {
        jQuery.post('#', {enable_tracker:id, wp_nonce:nonce});
        location.reload();
    })
}

function toggle(id,nonce) {
    jQuery.post('#', {toggle:id, wp_nonce: nonce});
    location.reload();
}