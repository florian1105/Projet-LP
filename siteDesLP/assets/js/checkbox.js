$("input:checkbox").click(function() {
    
    if($(this).attr("name") == "form[classes][]")
    {
        $("input[name$='article_public']:checkbox").prop("checked", false);
    }
    else
    {

        $("input[name$='form[classes][]']:checkbox").each(function() {
        
            $(this).prop("checked", false);
        });
    }

});


