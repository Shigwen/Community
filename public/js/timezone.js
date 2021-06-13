// Change la liste des timezones au chargement de la page
checkCountry();

// Change la liste des timezones au changement d'un Country
$('#user_country').on('change', function() {
    checkCountry();
});

/**
 * Permet de changer la liste des Timezone en fonction du Country choisi
 */
function checkCountry() {
    let country = $('#user_country').val();

    $.get( "/ajax/list-timezones-for/" + country, function(response) {
        $('#timezoneContainer').html('');
        $('#user_timezone').remove();
        $('#timezoneContainer').append(response.html);
      })
    .fail(function() {
        console.log("Error");
    });
}
