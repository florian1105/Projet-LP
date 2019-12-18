if($("input[name$='form[classes][]']:checked").length == 0) $("input[name$='article_public']:checkbox").prop("checked", true);

$("input:checkbox").click(function() {

    if($(this).attr("name") == "form[classes][]")
    {
        if($("input[name$='form[classes][]']:checked").length == 0) $("input[name$='article_public']:checkbox").prop("checked", true);
        else $("input[name$='article_public']:checkbox").prop("checked", false);
    }
    else if($(this).attr("name") == "article_public")
    {
        if($(this).not(":checked")) $(this).prop("checked", true);
        $("input[name$='form[classes][]']:checkbox").each(function() {
        
            $(this).prop("checked", false);
        });
    }

});


