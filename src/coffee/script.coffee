$ ->
    $('#eyecatch').css('background-position', '2400px 0')
    $('#eyecatch').animate({
        backgroundPosition:'0px'
    }, 40000, 'linear')
    setInterval (()->
        $('#eyecatch').css('background-position', '2400px 0')
        $('#eyecatch').animate({
            backgroundPosition:'0px'
        }, 40000, 'linear')
        console.log "hel"
    ), 41000
