/* Prepare l'affichage de la popup */
function prepareModal(idParent) {
	/* Ajoute à la popup le dossier dans
	   lequel ce cours doit etre ajouter
	   dans un champs caché */
	let coursParent = document.getElementById('form_coursParent')
	const classes = document.getElementsByName('form[classes][]')

	if (coursParent.value != idParent) {
		// Dossier dans lequel on insert
		coursParent.value = idParent

		// Reset nom + focus
		let nom = document.getElementById('form_nom')
		nom.value = null
		nom.focus()
		
		// Reset classes
		for (let i = classes.length - 1; i >= 0; i--) {
			classes[i].checked = false
			classes[i].parentNode.removeAttribute('style')
		}
	}

	/* Restraint le choix des classes
	   du dossier/cours par rapport à
	   son père
	*/
	if (idParent != null) {
		// Recupère les id des classes du parent
		const classesParent = document.querySelectorAll('.node')[0].dataset.classes.split(',')
		
		for (let i = classes.length - 1; i >= 0; i--) {

			let inParent = false
	
			// Controle si les classes sont des classes du parent
			for (var j = classesParent.length - 1; j >= 0; j--) {
				
				if(classesParent[j]==classes[i].value) {
					inParent = true
				}

			}

			// Cache les classes qui ne sont pas dans la liste des classes du parent
			if (!inParent) {
				classes[i].parentNode.setAttribute('style', 'display:none;')
			}
		}
	} 
}