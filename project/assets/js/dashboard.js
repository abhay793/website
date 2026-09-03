/**
 * dashboard.js
 * Fetches live feedback data from dashboard-data.php and renders:
 *  - a line chart of the 14-day feedback trend
 *  - three doughnut charts (total / positive / negative)
 * Also animates the top stat cards counting up to their loaded values.
 */
(function () {
  'use strict';

  var BRASS = '#C9962C';
  var GREEN = '#2E7D5B';
  var RED = '#B84A3E';
  var INK = '#0B1220';

  function animateCount(el, target) {
    if (!el) return;
    var start = 0;
    var duration = 800;
    var startTime = null;

    function step(timestamp) {
      if (!startTime) startTime = timestamp;
      var progress = Math.min((timestamp - startTime) / duration, 1);
      el.textContent = Math.floor(progress * (target - start) + start);
      if (progress < 1) {
        window.requestAnimationFrame(step);
      } else {
        el.textContent = target;
      }
    }
    window.requestAnimationFrame(step);
  }

  function buildDoughnut(ctx, value, total, color) {
    var remainder = Math.max(total - value, 0);
    return new Chart(ctx, {
      type: 'doughnut',
      data: {
        labels: ['Count', 'Remaining'],
        datasets: [{
          data: total > 0 ? [value, remainder] : [1, 0],
          backgroundColor: total > 0 ? [color, '#ECEAE3'] : ['#ECEAE3', '#ECEAE3'],
          borderWidth: 0,
        }],
      },
      options: {
        responsive: true,
        cutout: '72%',
        animation: { animateRotate: true, duration: 900 },
        plugins: {
          legend: { display: false },
          tooltip: { enabled: total > 0 },
        },
      },
    });
  }

  document.addEventListener('DOMContentLoaded', function () {
    fetch('/client/dashboard-data.php', { credentials: 'same-origin' })
      .then(function (res) {
        if (res.status === 401) {
          window.location.href = '/client/login.php?reason=timeout';
          throw new Error('Session expired');
        }
        return res.json();
      })
      .then(function (data) {
        if (data.error) return;

        animateCount(document.getElementById('statTotal'), data.total);
        animateCount(document.getElementById('statPositive'), data.positive);
        animateCount(document.getElementById('statNegative'), data.negative);

        // Trend line chart.
        var trendCtx = document.getElementById('trendChart');
        if (trendCtx) {
          new Chart(trendCtx, {
            type: 'line',
            data: {
              labels: data.trend.labels,
              datasets: [{
                label: 'Feedback received',
                data: data.trend.values,
                borderColor: BRASS,
                backgroundColor: 'rgba(201, 150, 44, 0.12)',
                tension: 0.35,
                fill: true,
                pointRadius: 3,
                pointBackgroundColor: INK,
              }],
            },
            options: {
              responsive: true,
              plugins: { legend: { display: false } },
              scales: {
                y: { beginAtZero: true, ticks: { precision: 0 } },
              },
            },
          });
        }

        // Doughnut charts.
        var totalEl = document.getElementById('doughnutTotal');
        var posEl = document.getElementById('doughnutPositive');
        var negEl = document.getElementById('doughnutNegative');
        if (totalEl) buildDoughnut(totalEl, data.total, data.total, BRASS);
        if (posEl) buildDoughnut(posEl, data.positive, data.total, GREEN);
        if (negEl) buildDoughnut(negEl, data.negative, data.total, RED);
      })
      .catch(function (err) {
        console.error('Dashboard data load failed:', err);
      });
  });
})();
