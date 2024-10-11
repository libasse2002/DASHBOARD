const allSideMenu = document.querySelectorAll('#sidebar .side-menu.top li a');

allSideMenu.forEach(item=> {
	const li = item.parentElement;

	item.addEventListener('click', function () {
		allSideMenu.forEach(i=> {
			i.parentElement.classList.remove('active');
		})
		li.classList.add('active');
	})
});



document.querySelectorAll('.card').forEach(card => {
    const status = card.getAttribute('data-status'); // Supposons que le statut soit défini dans un attribut 'data-status'
    
	if (status === 'validée') {
        card.classList.add('validée');
    } else if (status === 'en_attente') {
        card.classList.add('en_attente');
    } else if (status === 'refusée') {
        card.classList.add('refusée');
    }
});

// TOGGLE SIDEBAR
const menuBar = document.querySelector('#content nav .bx.bx-menu');
const sidebar = document.getElementById('sidebar');

menuBar.addEventListener('click', function () {
	sidebar.classList.toggle('hide');
})



document.addEventListener('DOMContentLoaded', function() {
    // Fonction pour récupérer les données en temps réel
    function updateCounts() {
        fetch('get_submission_counts.php')
            .then(response => response.json())
            .then(data => {
                // Met à jour les éléments HTML avec les nouveaux comptes
                document.getElementById('count-pending').innerText = data.pending || 0;
                document.getElementById('count-approved').innerText = data.approved || 0;
                document.getElementById('count-rejected').innerText = data.rejected || 0;
            })
            .catch(error => console.error('Erreur lors de la récupération des données:', error));
    }

    // Mise à jour initiale
    updateCounts();

    // Mise à jour des données toutes les 10 secondes
    setInterval(updateCounts, 10000);
});






const searchButton = document.querySelector('#content nav form .form-input button');
const searchButtonIcon = document.querySelector('#content nav form .form-input button .bx');
const searchForm = document.querySelector('#content nav form');

searchButton.addEventListener('click', function (e) {
	if(window.innerWidth < 576) {
		e.preventDefault();
		searchForm.classList.toggle('show');
		if(searchForm.classList.contains('show')) {
			searchButtonIcon.classList.replace('bx-search', 'bx-x');
		} else {
			searchButtonIcon.classList.replace('bx-x', 'bx-search');
		}
	}
})





if(window.innerWidth < 768) {
	sidebar.classList.add('hide');
} else if(window.innerWidth > 576) {
	searchButtonIcon.classList.replace('bx-x', 'bx-search');
	searchForm.classList.remove('show');
}


window.addEventListener('resize', function () {
	if(this.innerWidth > 576) {
		searchButtonIcon.classList.replace('bx-x', 'bx-search');
		searchForm.classList.remove('show');
	}
})



const switchMode = document.getElementById('switch-mode');

switchMode.addEventListener('change', function () {
	if(this.checked) {
		document.body.classList.add('dark');
	} else {
		document.body.classList.remove('dark');
	}
})

/*declaration récente*/ 
function fetchRecentDeclarations() {
	fetch('recents.php')
	  .then(response => response.json())
	  .then(data => {
		const recentContainer = document.querySelector('.recent-declarations');
		recentContainer.innerHTML = '';
  
		data.forEach(declaration => {
		  const declarationElement = document.createElement('div');
		  declarationElement.className = 'declaration-item';
		  declarationElement.innerHTML = `
			<p><strong>Département:</strong> ${declaration.department}</p>
			<p><strong>Nom EC:</strong> ${declaration.nom_ec}</p>
			<p><strong>Heures CM:</strong> ${declaration.hours_cm}</p>
			<p><strong>Heures TD:</strong> ${declaration.hours_td}</p>
			<p><strong>Heures TP:</strong> ${declaration.hours_tp}</p>
			<p><strong>Date:</strong> ${declaration.date}</p>
			<p><strong>Statut:</strong> <span class="status ${declaration.statut.toLowerCase()}">${declaration.statut}</span></p>
		  `;
		  recentContainer.appendChild(declarationElement);
		});
	  });
  }
  
  // Appeler la fonction fetchRecentDeclarations toutes les 30 secondes
  setInterval(fetchRecentDeclarations, 30000);
  fetchRecentDeclarations(); // Appel initial au chargement de la page

  /*socket*/
  const socket = new WebSocket('ws://votre_serveur_websocket');

socket.addEventListener('message', function(event) {
  const data = JSON.parse(event.data);
  if (data.type === 'new_declaration') {
    fetchRecentDeclarations(); // Met à jour les déclarations récentes immédiatement
  }
  document.querySelectorAll('.validate-btn, .reject-btn, .modify-button').forEach(button => {
	button.addEventListener('click', () => {
		button.classList.add('clicked');
		setTimeout(() => button.classList.remove('clicked'), 150);
	});
});
});
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.validate-btn').forEach(button => {
        button.addEventListener('click', () => {
            const ficheId = button.getAttribute('data-id');
            updateStatus(ficheId, 'validée');
        });
    });

    document.querySelectorAll('.reject-btn').forEach(button => {
        button.addEventListener('click', () => {
            const ficheId = button.getAttribute('data-id');
            updateStatus(ficheId, 'refusée');
        });
    });

    // Fonction pour envoyer la requête AJAX de validation ou de refus
    function updateStatus(ficheId, status) {
        fetch('update_fiche_status.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ fiche_id: ficheId, statut: status })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload(); // Recharge la page pour voir les modifications
            } else {
                alert('Erreur: ' + data.message);
            }
        })
        .catch(error => console.error('Erreur:', error));
    }
});


