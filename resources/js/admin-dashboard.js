import { Chart, registerables } from 'chart.js';
import './dashboard-realtime';

Chart.register(...registerables);
window.Chart = Chart;
window.dispatchEvent(new Event('dashboard-chart-ready'));
