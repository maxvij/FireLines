var buttons = document.getElementsByClassName('button');
for(var i = 0; i < buttons.length; i++) {
    buttons[i].addEventListener('click', function(e) {
        e.preventDefault();
        var prevButton = document.getElementsByClassName('button active');
        prevButton[0].setAttribute('class', 'button');
        e.target.parentNode.setAttribute('class', 'button active');
        var link = e.target.getAttribute('href').substr(1);
        var prevTabs = document.getElementsByClassName('tab-pane active');
        prevTabs[0].setAttribute('class', 'tab-pane');
        document.getElementById(link).setAttribute('class', 'tab-pane active');
    })
}