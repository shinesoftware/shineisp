$(document).ready(function() {
    $('#pageToc').generateToc({
        'content'  : '#wiki',
        'start'    : 3, 
        'listType' : 'ol' 
    });
});