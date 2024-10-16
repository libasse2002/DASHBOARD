form.addEventListener('submit', function(event) {
    event.preventDefault(); // Empêche le rechargement de la page
    console.log("Formulaire soumis");
 
    const formData = new FormData(form);
 
    fetch('submit.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        console.log("Réponse reçue");
        return response.json();
    })
    .then(data => {
        console.log(data); // Voir les données reçues
        if (data.success) {
            toastr.success('Votre soumission a été envoyée avec succès!');
            ws.send(JSON.stringify({ type: 'new_submission', teacher_name: data.teacher_name }));
        } else {
            toastr.error(data.message || 'Erreur lors de l\'envoi de la soumission.');
        }
    })
    .catch(error => console.error('Erreur:', error));
 });


 
 