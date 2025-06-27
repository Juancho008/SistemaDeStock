$(document).ready(function () {
	$("#pedidosTabla").DataTable({
	  language: {
		url: "https://cdn.datatables.net/plug-ins/1.10.16/i18n/Spanish.json",
	  },
	  order: [[2, "desc"]],
	  lengthMenu: [50, 100, 150, 200],
	});
  });
