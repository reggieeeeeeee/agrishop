function updateTimeAgo() {
    document.querySelectorAll(".time").forEach(function (element) {
        let createdAt = new Date(element.getAttribute("data-time"));
        let now = new Date();
        let timeDiff = Math.floor((now - createdAt) / 1000); // Difference in seconds

        let displayTime;
        if (timeDiff < 60) displayTime = timeDiff + "s ago";
        else if (timeDiff < 3600) displayTime = Math.floor(timeDiff / 60) + "m ago";
        else if (timeDiff < 86400) displayTime = Math.floor(timeDiff / 3600) + "h ago";
        else if (timeDiff < 2592000) displayTime = Math.floor(timeDiff / 86400) + "d ago";
        else if (timeDiff < 31536000) displayTime = Math.floor(timeDiff / 2592000) + "mo ago";
        else displayTime = Math.floor(timeDiff / 31536000) + "y ago";

        element.textContent = displayTime;
    });
}

// Update every 30 seconds
setInterval(updateTimeAgo, 30000);
window.onload = updateTimeAgo;