import io from 'socket.io-client';

const realtimeConfig = window.dashboardRealtimeConfig || {};
const realtimeToken = realtimeConfig.token;
const wsUrl = realtimeConfig.wsUrl || '';

if (!realtimeToken || !wsUrl) {
    console.warn('Realtime disabled: missing token.');
} else {
    const socket = io(wsUrl, {
        transports: ['websocket'],
        reconnection: true,
        reconnectionDelay: 1000,
        reconnectionDelayMax: 5000,
        reconnectionAttempts: 10,
    });

    socket.on('connect', () => {
        socket.emit('subscribe', {
            channel: 'dashboard',
            token: realtimeToken,
        });
    });

    socket.on('activity.added', (data) => {
            // Update the dashboard with new activity data
            console.log('New activity added:', data);

            // Update Latest Activity section
            const latestActivityName = document.querySelector('.latest-activity-name');
            const latestActivityTime = document.querySelector('.latest-activity-time');

            if (latestActivityName && latestActivityTime) {
                latestActivityName.textContent = data.activity.user_name;
                latestActivityTime.textContent = data.activity.activity_at;

                // Add flash animation
                const latestActivityCard = document.querySelector('.latest-activity-card');
                if (latestActivityCard) {
                    latestActivityCard.style.transition = 'background-color 0.3s ease';
                    latestActivityCard.style.backgroundColor = 'rgba(34, 197, 94, 0.1)';
                    setTimeout(() => {
                        latestActivityCard.style.backgroundColor = 'transparent';
                    }, 2000);
                }
            }

            // Fetch updated dashboard data to refresh all metrics
            fetch(window.location.href)
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const newDoc = parser.parseFromString(html, 'text/html');

                    // Update total activities count
                    const oldTotal = document.querySelector('[data-dashboard-total-activities]');
                    const newTotal = newDoc.querySelector('[data-dashboard-total-activities]');
                    if (oldTotal && newTotal) {
                        const oldValue = parseInt(oldTotal.textContent.replace(/,/g, ''), 10);
                        const newValue = parseInt(newTotal.textContent.replace(/,/g, ''), 10);
                        if (newValue > oldValue) {
                            oldTotal.textContent = newValue.toLocaleString();
                            oldTotal.style.transition = 'background-color 0.3s ease';
                            oldTotal.style.backgroundColor = 'rgba(34, 197, 94, 0.1)';
                            setTimeout(() => {
                                oldTotal.style.backgroundColor = 'transparent';
                            }, 2000);
                        }
                    }

                    // Update today activities count
                    const oldToday = document.querySelector('[data-dashboard-today-activities]');
                    const newToday = newDoc.querySelector('[data-dashboard-today-activities]');
                    if (oldToday && newToday) {
                        const oldValue = parseInt(oldToday.textContent.replace(/,/g, ''), 10);
                        const newValue = parseInt(newToday.textContent.replace(/,/g, ''), 10);
                        if (newValue > oldValue) {
                            oldToday.textContent = newValue.toLocaleString();
                            oldToday.style.transition = 'background-color 0.3s ease';
                            oldToday.style.backgroundColor = 'rgba(34, 197, 94, 0.1)';
                            setTimeout(() => {
                                oldToday.style.backgroundColor = 'transparent';
                            }, 2000);
                        }
                    }
                })
                .catch(error => console.error('Error fetching updated dashboard:', error));
    });
}
