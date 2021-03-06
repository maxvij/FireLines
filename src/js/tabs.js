var buttons = document.getElementsByClassName('button');
for (var i = 0; i < buttons.length; i++) {
    buttons[i].addEventListener('click', function (e) {
        e.preventDefault();
        var prevButton = document.getElementsByClassName('button active');
        prevButton[0].setAttribute('class', 'button');
        var link;
        if (e.target.tagName === 'A') {
            e.target.parentNode.setAttribute('class', 'button active');
            link = e.target.getAttribute('href').substr(1);
        } else {
            e.target.setAttribute('class', 'button active');
            link = e.target.firstChild.getAttribute('href').substr(1);
        }
        var prevTabs = document.getElementsByClassName('tab-pane active');
        prevTabs[0].setAttribute('class', 'tab-pane');
        document.getElementById(link).setAttribute('class', 'tab-pane active');
    })
}