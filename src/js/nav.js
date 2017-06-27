var menuTrigger = document.getElementsByClassName('menu-trigger');
var menuList = document.getElementById('menu-list');
menuTrigger[0].addEventListener('click', function() {
    if(menuTrigger[0].getAttribute('class') === 'menu-trigger') {
        menuList.setAttribute('class', 'show');
        menuTrigger[0].setAttribute('class', 'menu-trigger open');
    } else {
        menuList.setAttribute('class', '');
        menuTrigger[0].setAttribute('class', 'menu-trigger');
    }
});