
require('../css/treeStructure.css');

/* Doit être chargé après la
génération de l'arborescence */
let nodes = document.getElementsByClassName("node")

for (let i = 0; i < nodes.length; i++) {
	nodes[i].addEventListener("click", function() {
		this.parentElement.querySelector(".nest").classList.toggle("active")
		this.classList.toggle("active")
	});
}