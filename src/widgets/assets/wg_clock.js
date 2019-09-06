$(document).ready(function () {
    function addZero(i) {
        if (i < 10) {
            i = "0" + i;
        }
        return i;
    }
    $('.wg_clock .jquery-clock[data-jquery-clock]').each(function () {
        var seconds = new Date().getSeconds(),
            hours = new Date().getHours(),
            mins = new Date().getMinutes(),
            sdegree = seconds * 6,
            hdegree = hours * 30 + (mins / 2),
            mdegree = mins * 6;
        var updateWatch = function () {
            var t = $('.wg_clock .jquery-clock');
            sdegree += 6;
            if (sdegree % 360 == 0) {
                mdegree += 6;
            }
            hdegree += (0.1 / 12);
            var srotate = "rotate(" + sdegree + "deg)",
                hrotate = "rotate(" + hdegree + "deg)",
                mrotate = "rotate(" + mdegree + "deg)";
            $(".jquery-clock-sec", t).css({
                "-moz-transform": srotate,
                "-webkit-transform": srotate,
                '-ms-transform': srotate
            });
            $(".jquery-clock-hour", t).css({
                "-moz-transform": hrotate,
                "-webkit-transform": hrotate,
                '-ms-transform': hrotate
            });
            $(".jquery-clock-min", t).css({
                "-moz-transform": mrotate,
                "-webkit-transform": mrotate,
                '-ms-transform': mrotate
            });
            if (undefined !== $('.wg_clock_digital')) {
                var date = new Date();
                $('.wg_clock_digital .wg_hour').text(addZero(date.getHours()));
                $('.wg_clock_digital .wg_min').text(addZero(date.getMinutes()));
                $('.wg_clock_digital .wg_sec').text(addZero(date.getSeconds()));
            }
        }
        updateWatch();
        setInterval(function () {
            $(".jquery-clock-sec, .jquery-clock-hour, .jquery-clock-min").addClass('jquery-clock-transitions');
            updateWatch();
        }, 1000);
        $(window).focus(function () {
            $(".jquery-clock-sec, .jquery-clock-hour, .jquery-clock-min").addClass('jquery-clock-transitions');
        });
        $(window).blur(function () {
            $(".jquery-clock-sec, .jquery-clock-hour, .jquery-clock-min").removeClass('jquery-clock-transitions');
        });
    });
});
