function validerFiche(ficheId) {
    fetch('valider_fiche.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({ id: ficheId })
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        alert('Fiche validée avec succès');
        location.reload();
      } else {
        alert('Erreur lors de la validation');
      }
    });
  }
  
  function refuserFiche(ficheId) {
    fetch('refuser_fiche.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({ id: ficheId })
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        alert('Fiche refusée avec succès');
        location.reload();
      } else {
        alert('Erreur lors du refus');
      }
    });
  }
  