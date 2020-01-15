$(document).ready(function() 
{
    const table = $('#tab').removeAttr('width').DataTable(
    {
        select: true,
        scroll : 2000,
        lengthChange : false ,
        pageLength : 20,
        autoWidth : true ,
        order : [[0,"desc"]],
            columnDefs :
        [
            { width: 200, target: 7 }
        ],
        "language": 
        {
            "sEmptyTable":     "Aucune donnée",
            "sInfo":           "Affichage de l'élément _START_ à _END_ sur _TOTAL_ éléments",
            "sInfoEmpty":      "Affichage de l'élément 0 à 0 sur 0 élément",
            "sInfoFiltered":   "(filtré à partir de _MAX_ éléments au total)",
            "sInfoPostFix":    "",
            "sInfoThousands":  ",",
            "sLengthMenu":     "Afficher _MENU_ éléments",
            "sLoadingRecords": "Chargement...",
            "sProcessing":     "Traitement...",
            "sSearch":         "Rechercher :",
            "sZeroRecords":    "Aucun élément correspondant trouvé",
            "oPaginate": 
            {
                "sFirst":    "Premier",
                "sLast":     "Dernier",
                "sNext":     "Suivant",
                "sPrevious": "Précédent"
            },
            "oAria": 
            {
                "sSortAscending":  ": activer pour trier la colonne par ordre croissant",
                "sSortDescending": ": activer pour trier la colonne par ordre décroissant"
            },
            "select": 
            {
                    "rows": 
                    {
                        "_": "%d lignes sélectionnées",
                        "0": "Aucune ligne sélectionnée",
                        "1": "1 ligne sélectionnée"
                    }
            }
        }

    });

    $('#container').css( 'display', 'block' );
    table.columns.adjust().draw()

});

$( ".tableau-donnees" ).mouseout(function() 
{
    $( ".edit-btn" ).each(function() 
    {
        $(this).addClass("invisible");
    }); 
});

$( ".ligne" ).mouseover(function() 
{
    $( ".edit-btn" ).each(function() 
    {
        $(this).addClass("invisible");
    }); 

    $(this).children("td").children("a").removeClass("invisible");

});


        