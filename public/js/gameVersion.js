// Change la liste des serveurs au chargement de la page
checkGameVersion();

// Change la liste des serveurs au changement d'une version de jeu
$('#character_gameVersion').on('change', function() {
    checkGameVersion();
});

/**
 * Permet de changer la liste des serveurs en fonction de la version de jeu choisi
 */
function checkGameVersion() {
    let gameVersion = $('#character_gameVersion').val();

    $.ajax({
        url: "/ajax/list-servers-for-game-version/" + gameVersion,
        data: {
            idCharacter: $('#character').data('id')
        },
        success: (response) => {
            $('#serverContainer').html('');
            $('#character_server').remove();
            $('#serverContainer').append(response.html);
          },
          fail: () => {
            console.log("Error");
        }
      });
}
