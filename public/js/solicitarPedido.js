let carritoPedidos = [];
let modalInstance = null;
let tablaPedidos;

//Descuento Stock
document.addEventListener("click", function (e) {
  if (e.target.matches("button[data-id]")) {
    const id = e.target.dataset.id;
    const fila = e.target.closest("tr");
    const nombre = fila.children[1].textContent.trim();
    const stockOriginal = parseInt(fila.children[5].dataset.originalStock);

    const existente = carritoPedidos.find((p) => p.id === id);

    if (existente) {
      if (existente.cantidad >= stockOriginal) {
        alert("No hay más stock disponible");
        return;
      }
      existente.cantidad += 1;
    } else {
      carritoPedidos.push({
        id: id,
        nombre: nombre,
        cantidad: 1,
        stock: stockOriginal,
      });
    }

    // Actualizo toda la tabla visual y botones, no solo la fila actual
    actualizarStockVisual();
    console.log("Carrito actual:", carritoPedidos);
  }
});

//Llamado de la tabla de pedidos
$(document).ready(function () {
  armarTablaPedidos();
});

//Obtencion de datos de la API
async function armarTablaPedidos() {
  try {
    const response = await fetch(RUTA_URL + "/Api/obtenerProductosJson/", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
    });
    if (!response.ok) throw new Error("Error al obtener productos");
    const data = await response.json();
    cargarTabla(data);
  } catch (error) {
    console.error("Error en armarTablaPedidos:", error);
  }
}

//Armado de la tabla de pedidos
function cargarTabla(productos) {
  const tbody = document.querySelector("table tbody");
  tbody.innerHTML = "";
  productos.forEach((producto, index) => {
    const stockTotal = Array.isArray(producto.stock)
      ? producto.stock
          .filter((s) => s.estado === "0")
          .reduce((acc, s) => acc + parseInt(s.cantidad || 0), 0)
      : 0;

    const fila = document.createElement("tr");
    fila.dataset.id = producto.id;
    fila.innerHTML = `
      <th scope="row">${index + 1}</th>
      <td>${producto.nombre}</td>
      <td>${producto.categoria.nombre}</td>
      <td><img src="${
        producto.imagen_base64
      }" style="width:150px; height:100px; border-radius:5px;"/></td>
      <td>${producto.codigo_barra}</td>
      <td data-original-stock="${stockTotal}">${stockTotal}</td>
      <td>
        ${
          stockTotal > 0
            ? `<button class="btn btn-sm btn-success" data-id="${producto.id}">Agregar</button>`
            : `<span class="text-danger fw-bold">Stock no disponible</span>`
        }
      </td>
    `;
    tbody.appendChild(fila);
  });

  // Si ya existe la tabla, destruirla primero
  if ($.fn.DataTable.isDataTable("#pedidosTable")) {
    $("#pedidosTable").DataTable().destroy();
  }
  // Volver a inicializar DataTables
  $("#pedidosTable").DataTable({
    language: {
      url: "https://cdn.datatables.net/plug-ins/1.10.16/i18n/Spanish.json",
    },
    order: [[0, "asc"]],
    lengthMenu: [50, 100, 150, 200],
  });
}

//Armado del modal de pedidos
function solicitarPedido() {
  const lista = document.getElementById("listaCarrito");
  lista.innerHTML = "";

  if (carritoPedidos.length === 0) {
    lista.innerHTML =
      "<li class='list-group-item'>No hay productos seleccionados.</li>";
  } else {
    carritoPedidos.forEach((item, index) => {
      const li = document.createElement("li");
      li.className =
        "list-group-item d-flex justify-content-between align-items-center";
      li.innerHTML = `
        <span><strong>${item.nombre}</strong></span>
        <div class="d-flex align-items-center">
          <input type="number" min="1" max="${item.stock}" value="${item.cantidad}" 
                 class="form-control form-control-sm me-2 cantidad-input" data-index="${index}" style="width:80px; margin-right: 3rem !important;">
          <button class="btn btn-sm btn-danger" data-index="${index}">Quitar</button>
        </div>
      `;
      lista.appendChild(li);
    });

    // Cambios en cantidad
    lista.querySelectorAll(".cantidad-input").forEach((input) => {
      input.addEventListener("input", function () {
        const index = parseInt(this.dataset.index);
        let nuevaCantidad = parseInt(this.value);

        if (isNaN(nuevaCantidad) || nuevaCantidad < 1) {
          this.value = carritoPedidos[index].cantidad;
          setTimeout(() => actualizarStockVisual(), 0);
          return;
        }
        if (nuevaCantidad > carritoPedidos[index].stock) {
          alert("No hay suficiente stock disponible");
          this.value = carritoPedidos[index].stock;
          setTimeout(() => actualizarStockVisual(), 0);
          return;
        }

        carritoPedidos[index].cantidad = nuevaCantidad;
        actualizarStockVisual();
      });
    });

    // Botón quitar producto
    lista.querySelectorAll("button[data-index]").forEach((btn) => {
      btn.addEventListener("click", function () {
        const index = parseInt(this.dataset.index);
        carritoPedidos.splice(index, 1);
        solicitarPedido();
        actualizarStockVisual();
      });
    });
  }

  if (!modalInstance) {
    modalInstance = new bootstrap.Modal(
      document.getElementById("carritoModal")
    );
  }
  modalInstance.show();
}

// Actualizo en tiempo real el stock visual de los productos
function actualizarStockVisual() {
const filas = document.querySelectorAll("table tbody tr");
filas.forEach((fila) => {
  const id = fila.dataset.id; // <-- Siempre disponible
  const stockCelda = fila.children[5];
  const botonCelda = fila.children[6];

  const stockOriginal = parseInt(stockCelda.dataset.originalStock);
  const productoEnCarrito = carritoPedidos.find((p) => p.id === id);
  const usado = productoEnCarrito?.cantidad || 0;
  const stockRestante = stockOriginal - usado;

  stockCelda.textContent = stockRestante;

  if (stockRestante <= 0) {
    botonCelda.innerHTML = `<span class="text-danger fw-bold">Stock no disponible</span>`;
  } else {
    botonCelda.innerHTML = `<button class="btn btn-sm btn-success" data-id="${id}">Agregar</button>`;
  }
});
}
