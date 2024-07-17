$(document).ready(function () {
    $('#logoutLink').on('click', function (e) {
        sessionStorage.clear();
    });

    fetchAndDisplayNotifications();

    // Event listeners for the filters
    $('#allFilter').on('click', function() {
        fetchAndDisplayNotifications('all');
        // Update filter UI
        $('#allFilter').addClass('active-filter');
        $('#unreadFilter').removeClass('active-filter');
    });

    $('#unreadFilter').on('click', function() {
        fetchAndDisplayNotifications('unread');
        // Update filter UI
        $('#unreadFilter').addClass('active-filter');
        $('#allFilter').removeClass('active-filter');
    });
});

function redirectToInventory(id, type) {
    if (type === 'medicine') {
        window.location.href = `Inventory.php?MedicineBrandID=${id}`;
    } else if (type === 'equipment') {
        window.location.href = `equipment-inventory.php?EquipmentID=${id}`;
    }
}

function dismissNotification(id, type) {
    let data = {};

    // Handle different notification types
    if (type === 'low_stock' || type === 'no_stock' || type === 'expiring_stock') {
        data.medicine_brand_id = id;
    } else if (type === 'equipment_low_stock' || type === 'equipment_no_stock') {
        data.equipment_id = id;
    } else {
        console.error('Invalid notification type:', type);
        return;
    }

    console.log('Sending dismiss request:', data);

    fetch('dismiss_notification.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => {
        console.log('Dismiss response:', response);
        if (!response.ok) {
            throw new Error('Dismiss request failed');
        }
        // Check response type before parsing as JSON
        const contentType = response.headers.get('content-type');
        if (contentType && contentType.includes('application/json')) {
            return response.json(); // Attempt to parse response as JSON
        } else {
            throw new Error('Invalid JSON response from server');
        }
    })
    .then(data => {
        console.log('Dismiss response data:', data);
        // Update UI to remove unread indicator for the dismissed notification
        const notificationElement = document.getElementById(`notification-${id}`);
        if (notificationElement) {
            notificationElement.classList.remove('unread');
            const unreadIndicator = notificationElement.querySelector('.unread-indicator');
            if (unreadIndicator) {
                unreadIndicator.remove();
            }
        }
        // Optionally, perform additional UI updates or actions upon successful dismissal
    })
    .catch(error => {
        // Handle fetch errors or parsing errors
        console.error('Error dismissing notification:', error);
        
        // Log specific error details if needed
        if (error instanceof TypeError || error.message === 'Invalid JSON response from server') {
            response.text().then(text => {
                console.error('Dismiss response text:', text);
            });
        } else {
            console.error('Dismiss request failed:', error.message);
        }
    });
}

function fetchAndDisplayNotifications(filter = 'all') {
    const notificationContainer = document.getElementById('notificationContainer');

    function fetchAndGenerateNotifications(endpoint) {
        return fetch(endpoint)
            .then(response => response.json())
            .then(data => {
                console.log('Notifications fetched:', data);

                // Update the notification count
                const notificationCount = data.count;
                document.querySelector('.badge-notif').textContent = notificationCount;

                // Generate notification HTML
                const notificationsHTML = data.notifications.map(item => {
                    const isUnread = !item.dismissed || (new Date() - new Date(item.dismissed)) <= (24 * 60 * 60 * 1000);
                    const showNotification = filter === 'all' || (filter === 'unread' && isUnread);

                    if (showNotification) {
                        let redirectURL = '';
                        let id = '';
                        if (item.type === 'low_stock' || item.type === 'no_stock' || item.type === 'expiring_stock') {
                            redirectURL = `Inventory.php?MedicineBrandID=${item.MedicineBrandID}`;
                            id = item.MedicineBrandID;
                        } else if (item.type === 'equipment_no_stock' || item.type === 'equipment_low_stock') {
                            redirectURL = `equipment-inventory.php?EquipmentID=${item.EquipmentID}`;
                            id = item.EquipmentID;
                        }

                        let notificationText = '';
                        if (item.type === 'low_stock') {
                            notificationText = `<strong><div style="line-height:15px !important;"><span style="font-size:15px;">Low Stock</strong></span> <br><span style="font-size:13px;"> The current stock for ${item.BrandName} is at ${item.TotalQuantity}.</span> </div>`;
                        } else if (item.type === 'no_stock') {
                            notificationText = `<strong><div style="line-height:15px !important;"><span style="font-size:15px;"> No Stock Available </strong> </span> <br><span style="font-size:13px;"> There is no more stock for ${item.BrandName}. </span></div>`;
                        } else if (item.type === 'expiring_stock') {
                            const expiryDate = formatDate(item.StockExpiryDate);
                            notificationText = `<strong><div style="line-height:15px !important;"><span style="font-size:15px;">Expiring Stock</span></strong> <br><span style="font-size:13px;">Stock for ${item.BrandName} is expiring soon. Expiry Date is on ${expiryDate}</span> </div>`;
                        } else if (item.type === 'equipment_no_stock') {
                            notificationText = `<strong> <div style="line-height:15px !important;"><span style="font-size:15px;"> No Equipment Stock Available</strong> </span><br><span style="font-size:13px;"> There is no more stock for ${item.EquipmentName}.</span> </div>`;
                        } else if (item.type === 'equipment_low_stock') {
                            notificationText = `<strong><div style="line-height:15px !important;"><span style="font-size:15px;"> Low Equipment Stock</strong> </span> <br> <span style="font-size:13px;"> ${item.EquipmentName} is low on stock. The current stock for  ${item.EquipmentName} is at  ${item.Quantity} </span> </div>`;
                        }

                        let unreadIndicator = '';
                        if (isUnread) {
                            unreadIndicator = `<span class="unread-indicator"></span>`;
                        }

                        return `
                            <div id="notification-${id}" class="notification-item card mb-2 ${isUnread ? 'unread' : ''}" onclick="handleNotificationClick('${id}', '${item.type}', '${redirectURL}')" style="cursor: pointer;">
                                <div class="card-body">
                                    ${notificationText}
                                    ${unreadIndicator}
                                </div>
                            </div>
                        `;
                    } else {
                        return ''; // Don't display if not matching the filter
                    }
                }).join('');
                return notificationsHTML;
            })
            .catch(error => console.error('Error fetching data:', error));
    }

    notificationContainer.innerHTML = '';

    fetchAndGenerateNotifications('notifications.php')
        .then(notificationsHTML => {
            notificationContainer.innerHTML += notificationsHTML;
        })
        .catch(error => console.error('Error generating notifications:', error));

    setTimeout(() => fetchAndDisplayNotifications(filter), 15 * 60 * 1000); // Refresh notifications every 15 minutes
}

document.addEventListener('DOMContentLoaded', function() {
    const notifButton = document.querySelector('.button-notif');
    const notifDropdown = document.getElementById('notificationsDropdown');

    notifButton.addEventListener('click', function() {
        if (notifDropdown.style.display === 'block') {
            notifDropdown.style.display = 'none';
        } else {
            notifDropdown.style.display = 'block';
        }
    });

    // Close the dropdown if clicked outside
    window.addEventListener('click', function(event) {
        if (!notifButton.contains(event.target) && !notifDropdown.contains(event.target)) {
            notifDropdown.style.display = 'none';
        }
    });

    // Set active filter background color on page load
    $('#allFilter').addClass('active-filter');
});

function handleNotificationClick(id, type, redirectURL) {
    console.log('Notification clicked:', id, type);
    dismissNotification(id, type); // Dismiss the notification before redirecting
    window.location.href = redirectURL; // Redirect to the specified URL
}
function formatDate(dateString) {
    const date = new Date(dateString);
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return date.toLocaleDateString('en-US', options);
}
