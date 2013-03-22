/**
 * jQuery TOC Generator Plugin
 *
 * @example
 * @author Jasper Moelker, Active IDs <j.b.moelker@activeids.nl>
 * @author Arend van Waart <arendvw@dds.nl>
 * @version 0.3 (2010-03-10)
 * @licence 
 * @depends jQuery v1.3.1 or later
 * @package pageToc
 *
 * Requires subtle use of h1, h2, h3. Not using proper order might cause fubar situations. You have been warned.
 *
 * Based on the work of:
 *  1) Perry Trinier (http://www.thewebsitetailor.com/jquery-slug-plugin/)
 */
 
 
/**
 * between
 * Gets all elements of type target between firstEl and lastEl from $(this)
 * 
 * @param <DOMObject> firstEl dom object for lastelement
 * @param <string|DOMObject> Dom object or lastEl selector for last element
 * @param <string> target
 * @author AvW
 *
 * @lightlyinspiredby http://stackoverflow.com/questions/481076/jquery-how-to-select-all-content-between-two-tags
 */
jQuery.fn.between = function(firstEl, lastEl, target) {
    var collection = new Array(); // Collection of Elements
    var started = false;
    //collection.push(firstElement); // Add First Element to Collection
    $(this).find(target).each(function(){ // Traverse all siblings
 
        if (!started && (this == firstEl))
        {
            started = true;
            return true;
        } else if (!started) {
            return true;
        }
        if ($(this).is(lastEl) || this == lastEl) { // If Sib is not LastElement
            return false; // Break Loop
        } else { // Else, if Sib is LastElement
            collection.push(this);
            return true; // jQuery for continue;
        }
    });
    return $(collection); // Return Collection
};
 
/**
 * TOC Generator
 *
 * @param <object> options
 * @author JBM/AvW
 */
jQuery.fn.generateToc = function(options)
{
    var settings = {
        container: jQuery(this),  // div element to embed index in
        content: 'body', // div element to gather headings from
        listType: 'ol',   // show index in ordered list <ol> if true, unordered list <ul> if false
        start: 1,       // heading level start. eg start:1 will start at h1
        level: 3,      // heading level limit. eg level:2 will show h1, h2
        id: 'pageToc'
    };
 
    if(options) {
        jQuery.extend(settings, options);
    }
 
    $this = jQuery(this);
 
 
    /**
	 * @param <array> headings
	 */
    var saHeadings = [];
    for(var i=1; i <= settings.level; i++)
    {
        saHeadings.push('h' + i);
    }
 
    /**
	 * @param <array> headings
	 */
    var sListType = settings.listType;
 
    /**
	 * @param <int> headings
	 */
    var slugId = 1;
 
    /**
	 * createAnchor
	 * adds an <a name> tag and returns the slug for the generated title.
	 *
	 * @param <jQuery> target
	 * @author JBM/AvW
	 * @return <string> slug
	 */
    var createAnchor = function(target){
 
        var sTitle = target.html();
        var sSlug = sTitle;
        // 1 for h1 or 2 for h2 etc.
        var nLevel = target.context.nodeName.substring(1);
 
        /* CREATE SLUG */
        // TODO: This slug does not guarantee a unique title. Two titles with the name 'Example' might exist. Also a title called 'body' or 'container' might cause difficulty.
        // Solution:
        // prepend or postpend a unique id
        sSlug = sSlug.replace(/\s/g,'-');
        sSlug = sSlug.replace(/[^a-zA-Z0-9\-]/g,'');
        sSlug = sSlug.toLowerCase();
        sSlug += '-' + slugId++;
        // console.log(sText);
 
        /* CREATE ANCHOR */
        var sAnchor = sprintf('<a name="%s"></a>', sSlug);
        target.before(sAnchor);
        // console.log(sAnchor);
 
        return sSlug;
    }
 
    /**
	 * @param <string> response
	 */
    var response = '';
 
    /**
	 * recurse through h1 elements.
	 * appends <ul> and list elements to odered and unondered lists.
	 *
	 * @param <jQueryCollection> response
	 * @author AvW
	 */
    var recurse = function (collection)
    {
        if (!collection || collection.size() == 0)
        {
            // if it's an empty collection, just return and move on. No need to open or close ul tags.
            return;
        }
 
        // open list tag.
        response += sprintf('<%s>',settings.listType);
 
        var currentHeading = collection.get(0).tagName;
 
        // add a list item for each h* element.
        collection.each(
            function() {
                slug = createAnchor($(this));
                response += sprintf('<li><a href="#%s">%s',slug, $(this).html());
                recurse($(settings.content).between(this,currentHeading,getNextLevel(currentHeading) + "," + currentHeading));
                response += '</li>';
            });
        // close the list tag.
        response += sprintf('</%s>',settings.listType);
    }
 
    var getNextLevel = function(sHeading)
    {
        return 'h' + (parseInt(sHeading.replace(/[^\d]/,'')) + 1);
    };
 
    jQuery(document).ready( function() {
        recurse($('h'+settings.start,settings.content));
        settings.container.html(response);
    });
};
 
/**
 * sprintf() for JavaScript v.0.4
 *
 * Copyright (c) 2007 Alexandru Marasteanu <http://alexei.417.ro/>
 * Thanks to David Baird (unit test and patch).
 *
 * @author Alexandru Marasteanu <http://alexei.417.ro/>
 * @version 0.4
 * @license GNU Public Licence 2 or later
 */
 
function str_repeat(i, m) {
    for (var o = []; m > 0; o[--m] = i);
    return(o.join(''));
}
 
function sprintf () {
    var i = 0, a, f = arguments[i++], o = [], m, p, c, x;
    while (f) {
        if (m = /^[^\x25]+/.exec(f)) o.push(m[0]);
        else if (m = /^\x25{2}/.exec(f)) o.push('%');
        else if (m = /^\x25(?:(\d+)\$)?(\+)?(0|'[^$])?(-)?(\d+)?(?:\.(\d+))?([b-fosuxX])/.exec(f)) {
            if (((a = arguments[m[1] || i++]) == null) || (a == undefined)) throw("Too few arguments.");
            if (/[^s]/.test(m[7]) && (typeof(a) != 'number'))
                throw("Expecting number but found " + typeof(a));
            switch (m[7]) {
                case 'b':
                    a = a.toString(2);
                    break;
                case 'c':
                    a = String.fromCharCode(a);
                    break;
                case 'd':
                    a = parseInt(a);
                    break;
                case 'e':
                    a = m[6] ? a.toExponential(m[6]) : a.toExponential();
                    break;
                case 'f':
                    a = m[6] ? parseFloat(a).toFixed(m[6]) : parseFloat(a);
                    break;
                case 'o':
                    a = a.toString(8);
                    break;
                case 's':
                    a = ((a = String(a)) && m[6] ? a.substring(0, m[6]) : a);
                    break;
                case 'u':
                    a = Math.abs(a);
                    break;
                case 'x':
                    a = a.toString(16);
                    break;
                case 'X':
                    a = a.toString(16).toUpperCase();
                    break;
            }
            a = (/[def]/.test(m[7]) && m[2] && a > 0 ? '+' + a : a);
            c = m[3] ? m[3] == '0' ? '0' : m[3].charAt(1) : ' ';
            x = m[5] - String(a).length;
            p = m[5] ? str_repeat(c, x) : '';
            o.push(m[4] ? a + p : p + a);
        }
        else throw ("Huh ?!");
        f = f.substring(m[0].length);
    }
    return o.join('');
}
