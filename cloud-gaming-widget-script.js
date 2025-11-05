function switchTheme(theme) {
    const widget = document.getElementById('cloud-gaming-widget');
    widget.className = 'cloud-gaming-widget';
    widget.classList.add('theme-' + theme);
    
    widget.style.opacity = '0';
    widget.style.transform = 'scale(0.95)';
    
    setTimeout(() => {
        widget.style.transition = 'opacity 0.4s, transform 0.4s';
        widget.style.opacity = '1';
        widget.style.transform = 'scale(1)';
    }, 50);
}

document.addEventListener('DOMContentLoaded', function() {
    const toolItems = document.querySelectorAll('.tool-item');
    
    toolItems.forEach(item => {
        item.addEventListener('click', function(e) {
            const toolName = item.getAttribute('data-tool');
            console.log('Tool clicked:', toolName);
            
            item.style.transform = 'scale(0.97)';
            setTimeout(() => {
                item.style.transform = '';
            }, 200);
        });
    });

    const widget = document.getElementById('cloud-gaming-widget');
    widget.style.opacity = '0';
    widget.style.transform = 'scale(0.95)';
    
    setTimeout(() => {
        widget.style.transition = 'opacity 0.6s, transform 0.6s';
        widget.style.opacity = '1';
        widget.style.transform = 'scale(1)';
    }, 100);
});
