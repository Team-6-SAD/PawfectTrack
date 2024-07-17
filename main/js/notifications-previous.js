
    $(document).ready(function () {
        // Attach click event handler to the logout link
        $('#logoutLink').on('click', function (e) {
            // Clear sessionStorage
            sessionStorage.clear();
        });
    });


// Function to redirect to Inventory.php with MedicineBrandID or EquipmentID
function redirectToInventory(id, type) {
  if (type === 'medicine') {
    window.location.href = `Inventory.php?MedicineBrandID=${id}`;
  } else if (type === 'equipment') {
    window.location.href = `equipment-inventory.php?EquipmentID=${id}`;
  }
}

function fetchAndDisplayToasts() {
  const toastContainer = document.getElementById('toastContainer');
  
  // Function to fetch data and generate toast HTML
  function fetchAndGenerateToasts(endpoint) {
    return fetch(endpoint)
      .then(response => response.json())
      .then(data => {
        // Generate toast HTML based on type and dismissal status
        const toastHTML = data.map(item => {
          if (!item.dismissed_at || new Date(item.dismissed_at) < new Date(Date.now() - 15 * 60 * 1000)) {
            if (item.type === 'low_stock') {
              return `
                <div class="toast" role="alert" aria-live="assertive" aria-atomic="true" data-autohide="false">
                  <div class="toast-header" style="background-color:#FFC700F2 !important;">
                    <img src="img/img-dashboard/notif-warn.png" style="height:20px; width:auto;" class="mr-2">
                    <strong class="mr-auto" style="color:#775D00 !important;">Warning: Low Stock</strong>
                    <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close" data-id="${item.MedicineBrandID || item.EquipmentID}" data-type="${item.type === 'low_stock' ? 'medicine' : 'equipment'}">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="toast-body">
                    ${item.type === 'low_stock' ? `Stock for ${item.BrandName} is at ${item.TotalQuantity}.` : `Stock for ${item.EquipmentName} is low.`} <br>
                    ${item.type === 'low_stock' ? `<button type="button" class="btn btn-primary btn-sm mt-2" style="border:none !important ;background-color:#0449A6 !important; border-radius:27.5px !important;" onclick="redirectToInventory(${item.MedicineBrandID}, 'medicine')">` :
                    `<button type="button" class="btn btn-primary btn-sm mt-2" style="border:none !important ;background-color:#0449A6 !important; border-radius:27.5px !important;" onclick="redirectToInventory(${item.EquipmentID}, 'equipment')">`}
                      Go to Inventory
                    </button>
                  </div>
                </div>
              `;
            } else if (item.type === 'no_stock') {
              return `
                <div class="toast" role="alert" aria-live="assertive" aria-atomic="true" data-autohide="false">
                  <div class="toast-header" style="background-color:#FFC700F2 !important;">
                    <img src="img/img-dashboard/notif-warn.png" style="height:20px; width:auto;" class="mr-2">
                    <strong class="mr-auto" style="color:#775D00 !important;">No Stock Available</strong>
                    <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close" data-id="${item.MedicineBrandID}" data-type="medicine">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="toast-body">
                    There is no more stock for ${item.BrandName}. <br>
                    <button type="button" class="btn btn-primary btn-sm mt-2" style="border:none !important ;background-color:#0449A6 !important; border-radius:27.5px !important;" onclick="redirectToInventory(${item.MedicineBrandID}, 'medicine')">
                      Go to Inventory
                    </button>
                  </div>
                </div>
              `;
            } else if (item.type === 'expiring_stock') {
              return `
                <div class="toast" role="alert" aria-live="assertive" aria-atomic="true" data-autohide="false">
                  <div class="toast-header" style="background-color:#FFC700F2 !important;">
                    <img src="img/img-dashboard/notif-warn.png" style="height:20px; width:auto;" class="mr-2">
                    <strong class="mr-auto" style="color:#775D00 !important;">Warning: Expiring Stock</strong>
                    <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close" data-id="${item.MedicineBrandID}" data-type="medicine">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="toast-body">
                    Stock for ${item.BrandName} is expiring soon. <br>
                    Expiry Date: ${item.StockExpiryDate} <br>
                    <button type="button" class="btn btn-primary btn-sm mt-2" style="border:none !important ;background-color:#0449A6 !important; border-radius:27.5px !important;" onclick="redirectToInventory(${item.MedicineBrandID}, 'medicine')">
                      Go to Inventory
                    </button>
                  </div>
                </div>
              `;
            } else if (item.type === 'equipment_no_stock') {
              return `
                <div class="toast" role="alert" aria-live="assertive" aria-atomic="true" data-autohide="false">
                  <div class="toast-header" style="background-color:#FFC700F2 !important;">
                    <img src="img/img-dashboard/notif-warn.png" style="height:20px; width:auto;" class="mr-2">
                    <strong class="mr-auto" style="color:#775D00 !important;">No Equipment Stock Available</strong>
                    <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close" data-id="${item.EquipmentID}" data-type="equipment">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="toast-body">
                    There is no more stock for ${item.EquipmentName}. <br>
                    <button type="button" class="btn btn-primary btn-sm mt-2" style="border:none !important ;background-color:#0449A6 !important; border-radius:27.5px !important;" onclick="redirectToInventory(${item.EquipmentID}, 'equipment')">
                      Go to Inventory
                    </button>
                  </div>
                </div>
              `;
            }
   else if (item.type === 'equipment_low_stock') {
              return `
                <div class="toast" role="alert" aria-live="assertive" aria-atomic="true" data-autohide="false">
                  <div class="toast-header" style="background-color:#FFC700F2 !important;">
                    <img src="img/img-dashboard/notif-warn.png" style="height:20px; width:auto;" class="mr-2">
                    <strong class="mr-auto" style="color:#775D00 !important;">Low Equipment Stock </strong>
                    <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close" data-id="${item.EquipmentID}" data-type="equipment">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="toast-body">
                     ${item.EquipmentName} is low on stock . <br>
					Current Stock: ${item.Quantity} <br>
                    <button type="button" class="btn btn-primary btn-sm mt-2" style="border:none !important ;background-color:#0449A6 !important; border-radius:27.5px !important;" onclick="redirectToInventory(${item.EquipmentID}, 'equipment')">
                      Go to Inventory
                    </button>
                  </div>
                </div>
              `;
            }
          } else {
            return ''; // Don't display if dismissed within the last 15 minutes
          }
        }).join('');

        return toastHTML;
      })
      .catch(error => console.error('Error fetching data:', error));
  }

  // Function to dismiss notification on the server
  function dismissNotification(id, type) {
    let data = {};
    if (type === 'medicine') {
      data.medicine_brand_id = id;
    } else if (type === 'equipment') {
      data.equipment_id = id;
    }

    fetch('dismiss_notification.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(data)
    })
      .then(response => {
        if (!response.ok) {
          console.error('Failed to dismiss notification');
        }
      })
      .catch(error => {
        console.error('Error dismissing notification:', error);
      });
  }

  // Clear the toast container
  toastContainer.innerHTML = ''; 

  // Fetch and display toasts
  fetchAndGenerateToasts('notifications.php')
    .then(toastHTML => {
      // Append generated toast HTML to toastContainer
      toastContainer.innerHTML += toastHTML;

      // Initialize Bootstrap toasts
      const toastElements = toastContainer.querySelectorAll('.toast');
      toastElements.forEach(toastEl => {
        const toast = new bootstrap.Toast(toastEl);
        toast.show();
      });

      // Close toast on button click
      const closeButtons = toastContainer.querySelectorAll('.close');
      closeButtons.forEach(btn => {
        btn.addEventListener('click', () => {
          const id = btn.getAttribute('data-id');
          const type = btn.getAttribute('data-type');
          dismissNotification(id, type); // Dismiss notification on server
          const toast = btn.closest('.toast');
          const toastInstance = bootstrap.Toast.getInstance(toast);
          toastInstance.hide();
        });
      });
    })
    .catch(error => console.error('Error generating toasts:', error));

  // Reload notifications after 15 minutes
  setTimeout(fetchAndDisplayToasts, 15 * 60 * 1000); // 15 minutes in milliseconds
}

// Fetch and display toasts when the page loads
document.addEventListener('DOMContentLoaded', fetchAndDisplayToasts);
