Auto-refresh functionality
let refreshInterval;
let lastUpdateTime = new Date();

function startAutoRefresh() {
    refreshInterval = setInterval(refreshScoreboard, 30000); // 30 seconds
}

function refreshScoreboard() {
    const container = document.getElementById('scoreboardContainer');
    const lastUpdateSpan = document.getElementById('lastUpdate');
    
    // Show loading state
    container.style.opacity = '0.7';
    
    fetch('api/get_scores.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateScoreboardDisplay(data.scoreboard);
                lastUpdateSpan.textContent = new Date().toLocaleTimeString();
                lastUpdateTime = new Date();
            } else {
                console.error('Failed to refresh scoreboard:', data.message);
            }
        })
        .catch(error => {
            console.error('Error refreshing scoreboard:', error);
        })
        .finally(() => {
            container.style.opacity = '1';
        });
}

function updateScoreboardDisplay(scoreboard) {
    const container = document.getElementById('scoreboardContainer');
    
    if (scoreboard.length === 0) {
        container.innerHTML = `
            <div class="text-center py-5">
                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                <h3 class="text-muted">No participants yet</h3>
                <p class="text-muted">Participants will appear here once judges start scoring.</p>
            </div>
        `;
        return;
    }
    
    let html = '<div class="row">';
    
    scoreboard.forEach((participant, index) => {
        const progressWidth = scoreboard[0].total_points > 0 
            ? (participant.total_points / scoreboard[0].total_points) * 100 
            : 0;
            
        html += `
            <div class="col-lg-6 mb-3">
                <div class="scoreboard-item animate-in" style="animation-delay: ${index * 0.1}s">
                    <div class="rank-badge rank-${Math.min(index + 1, 3)}">
                        #${index + 1}
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="participant-info">
                            <h4 class="mb-1 fw-bold text-primary">
                                ${escapeHtml(participant.full_name)}
                            </h4>
                            <p class="mb-1 text-muted">
                                <i class="fas fa-user me-1"></i>
                                @${escapeHtml(participant.username)}
                            </p>
                            <small class="text-muted">
                                <i class="fas fa-gavel me-1"></i>
                                Scored by ${participant.total_judges} judge(s)
                            </small>
                        </div>
                        
                        <div class="text-end">
                            <div class="points-display" data-points="${participant.total_points}">
                                ${participant.total_points}
                            </div>
                            <small class="text-muted">Total Points</small>
                        </div>
                    </div>
                    
                    <div class="progress mt-3" style="height: 8px;">
                        <div class="progress-bar bg-gradient" 
                             role="progressbar" 
                             style="width: ${progressWidth}%">
                        </div>
                    </div>
                </div>
            </div>
        `;
    });
    
    html += '</div>';
    container.innerHTML = html;
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Initialize auto-refresh when page loads
document.addEventListener('DOMContentLoaded', function() {
    startAutoRefresh();
    
    // Add visual feedback for refresh button
    const refreshBtn = document.querySelector('button[onclick="refreshScoreboard()"]');
    if (refreshBtn) {
        refreshBtn.addEventListener('click', function() {
            const icon = this.querySelector('i');
            icon.classList.add('fa-spin');
            
            setTimeout(() => {
                icon.classList.remove('fa-spin');
            }, 1000);
        });
    }
});

// Cleanup interval on page unload
window.addEventListener('beforeunload', function() {
    if (refreshInterval) {
        clearInterval(refreshInterval);
    }