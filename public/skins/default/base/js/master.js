var contentSliderSpeed = 5000;
var animationSpeed = 200;

function parseDate(b) {
    var a = b.split(" ");
    return new Date(Date.parse(a[1] + " " + a[2] + ", " + a[5] + " " + a[3] + " UTC"))
}
var relativeDate = function (c) {
    var b = new Date();
    b.setTime(parseDate(c));
    var a = ((new Date() - b) / 1000);
    var d = Math.floor(a / 60);
    if (d == 0) {
        return "less than a minute ago"
    }
    if (d == 1) {
        return "a minute ago"
    }
    if (d < 45) {
        return d + " minutes ago"
    }
    if (d < 90) {
        return "about 1 hour ago"
    }
    if (d < 1440) {
        return "about " + Math.round(d / 60) + " hours ago"
    }
    if (d < 2880) {
        return "1 day ago"
    }
    if (d < 43200) {
        return Math.floor(d / 1440) + " days ago"
    }
    if (d < 86400) {
        return "about 1 month ago"
    }
    if (d < 525960) {
        return Math.floor(d / 43200) + " months ago"
    }
    if (d < 1051199) {
        return "about 1 year ago"
    }
    return "over " + Math.floor(d / 525960) + " years ago"
};

$(function () {
    var e = false;
    
    $("#header ul.navigation li").hover(function () {
        $(this).find("ul.dropdown").stop(true, true).hide().animate({
            height: "show"
        }, animationSpeed)
    }, function () {
        $(this).find("ul.dropdown").stop(true, true).show().animate({
            opacity: "hide"
        }, animationSpeed)
    });
    
    
    var a = $("#slideshow");
    var f = a.find("div.slide");
    if (f.length > 1) {
        var o = f.length;
        var q = Math.round((480 - ((f.length - 1) * 9)) / o) - 2;
        var p = '<div class="slideSwitch">';
        $.each(f, function (s) {
            if (s != 0) {
                $(this).hide()
            }
            var r = $(this).find(".slideSwitchLabel").html();
            if (r === null) {
                r = s + 1
            }
            $(this).attr("id", "slide-" + s);
            p += '<div id="switchSlide-' + s + '" style="width: ' + q + 'px;">' + r + "</div>"
        });
        a.css({
            height: "350px"
        });
        f.css({
            height: "350px",
            overflow: "visible"
        });
        a.find("div.slide img").css({
            marginTop: 0
        });
        a.find("div.center").prepend(p + "</div>");
        a.find("div.slideSwitch div:first").addClass("active");
        a.find("div.slideSwitch div:last").css({
            marginRight: 0
        });
        var h;
        var d = function () {
            var r = a.find("div.slideSwitch div.active").attr("id").split("-");
            r = r[1];
            if (r == (o - 1)) {
                r = 0
            } else {
                r++
            }
            $("#switchSlide-" + r).click();
            h = window.setTimeout(d, contentSliderSpeed)
        };
        h = window.setTimeout(d, contentSliderSpeed);
        var l = false;
        a.delegate("div.slideSwitch div", "click", function (r) {
            if ($(this).is(".active")) {
                return false
            }
            if (l) {
                $("#slideshow div.slide, #slideshow div.slide div.information, #slideshow div.slide img").stop(true, true)
            }
            l = true;
            if (typeof r.which !== "undefined") {
                window.clearTimeout(h)
            }
            var t = $(this).attr("id").split("-");
            t = t[1];
            $("#slideshow div.slideSwitch div").removeClass("active");
            $(this).addClass("active");
            var s = $("#slideshow .slide:visible");
            s.find("div.information").animate({
                marginLeft: "-420px",
                opacity: 0
            }, 400, function () {
                s.hide();
                $(this).css({
                    marginLeft: 0,
                    opacity: 1
                })
            });
            s.find("img").animate({
                marginRight: "-480px",
                opacity: 0
            }, 400, function () {
                $(this).css({
                    marginRight: 0,
                    opacity: 1
                })
            });
            $("#slide-" + t).css({
                position: "absolute",
                top: "-340px"
            }).show().animate({
                top: "0"
            }, 400, function () {
                $(this).css({
                    position: "relative"
                });
                l = false
            })
        })
    }
    $("a.imageZoom").click(function () {
        var s = $(this).attr("href");
        $("#container").prepend('<div class="siteOverlay"></div><div class="siteLoading"><div></div></div><img class="imageZoomBox" src="' + s + '" alt="Zoom" /><div class="imageZoomClose"></div>');
        $("div.siteOverlay").css({
            opacity: 0
        }).show().animate({
            opacity: 0.9
        }, animationSpeed);
        $("div.siteLoading").animate({
            opacity: "show"
        }, animationSpeed);
        var r = $("img.imageZoomBox");
        $(r).load(function () {
            var v = 0;
            var u = $(this).width();
            var t = $(this).height();
            var w = $(window).width();
            var z = $(window).height();
            var y = w - 80;
            var x = z - 80;
            if (u > y) {
                v = y / u;
                u = u * v;
                t = t * v
            }
            if (t > x) {
                v = x / t;
                u = u * v;
                t = t * v
            }
            $("div.siteLoading").hide();
            $(this).css({
                width: 0,
                height: 0
            }).animate({
                opacity: "show",
                width: u + "px",
                height: t + "px",
                marginTop: "-" + ((t / 2) + 20) + "px",
                marginLeft: "-" + ((u / 2) + 20) + "px"
            }, animationSpeed, function () {
                $("div.imageZoomClose").css({
                    marginLeft: ((u / 2) - 20) + "px",
                    marginTop: "-" + ((t / 2) + 20) + "px"
                }).animate({
                    opacity: "show"
                }, animationSpeed)
            })
        });
        return false
    });
    $("#container").delegate("div.siteOverlay, div.imageZoomClose, div.siteLoading", "click", function () {
        $("div.siteOverlay, img.imageZoomBox, div.imageZoomClose, div.siteLoading").animate({
            opacity: "hide"
        }, animationSpeed, function () {
            $(this).remove()
        })
    });
    if ($("div.tabWrapper").length != 0) {
        $("div.tabWrapper").each(function () {
            var t = '<ul class="tabs">';
            var s = $(this).find(".tabContent");
            var r = s.length;
            $(s).each(function (v) {
                if (v != 0) {
                    $(this).hide()
                }
                var u = $(this).find(".label").text();
                if (!u) {
                    u = "Tab " + (v + 1)
                }
                $(this).addClass("tab-" + v);
                t += '<li class="tabTrigger-' + v + '">' + u + "</li>"
            });
            $(this).prepend(t + "</ul>");
            $(this).find("li:first").addClass("active")
        })
    }
    $(".tabWrapper").delegate("ul.tabs li", "click", function () {
        if ($(this).is(".active")) {
            return false
        }
        var s = $(this).attr("class").split("-");
        s = s[1];
        var r = $(this).parent().parent();
        r.find("ul.tabs li").removeClass("active");
        $(this).addClass("active");
        r.find(".tabContent").hide();
        r.find(".tab-" + s).animate({
            opacity: "show"
        }, animationSpeed)
    });
    var k = $('label[for="name"]').text();
    var m = $('label[for="email"]').text();
    var c = $('label[for="message"]').text();
    $("#contactForm").submit(function () {
        var t = $("#name").val();
        if (t.length == 0) {
            $('label[for="name"]').addClass("error").text(k + " is required")
        } else {
            $('label[for="name"]').removeClass("error").text(k)
        }
        var s = $("#email").val();
        var r = new RegExp(/^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i);
        if (s.length == 0) {
            $('label[for="email"]').addClass("error").text(m + " is required")
        } else {
            if (!r.test(s)) {
                $('label[for="email"]').addClass("error").text(m + " is not valid")
            } else {
                $('label[for="email"]').removeClass("error").text(m)
            }
        }
        var u = $("#message").val();
        if (u.length == 0) {
            $('label[for="message"]').addClass("error").text(c + " is required")
        } else {
            $('label[for="message"]').removeClass("error").text(c)
        }
        if ($(this).find("label.error").length != 0) {
            return false
        } else {
            $(this).find("button").text("Please wait...")
        }
    });
    var n = $("form.blogSearch");
    if (n.length != 0) {
        var j = n.find("label").text();
        var i = n.find('input[type="text"]');
        i.val(j);
        i.focus(function () {
            if ($(this).val() == j) {
                $(this).val("");
                $(this).focus()
            }
        });
        i.blur(function () {
            if ($(this).val() == "") {
                $(this).val(j);
                $(this).blur()
            }
        })
    }
    var g = $("div.twitter");
    if (g.length != 0) {
        var b = g.find("a.profile").attr("href").replace("http://twitter.com/", "");
        $.getJSON("https://api.twitter.com/1/statuses/user_timeline.json?screen_name=" + b + "&include_rts=true&callback=?", function (r) {
            if (r && r[0] && r[0].text && r[0].created_at) {
                var s = r[0].text.replace(/(\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/ig, function (t) {
                    return '<a href="' + t + '">' + t + "</a>"
                });
                s = s.replace(/\B@([\w-]+)/gm, function (t) {
                    return '<a href="http://twitter.com/' + $.trim(t).replace("@", "") + '">' + t + "</a>"
                });
                s = s.replace(/(^|\s+)#(\w+)/gi, function (t) {
                    return '<a href="http://twitter.com/search?q=%23' + $.trim(t).replace("#", "") + '">' + t + "</a>"
                });
                g.find("p").html(s + "<span>" + relativeDate(r[0].created_at) + "</span>")
            } else {
                g.find("p").html("Error<span>Latest tweet could not be retrieved...</span>")
            }
        })
    }
    $("div.siteFooterBar a.backToTop").click(function () {
        $("html, body").animate({
            scrollTop: 0
        }, animationSpeed);
        return false
    })
});