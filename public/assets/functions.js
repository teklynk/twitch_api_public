// Format date/time to locale
function getDateTime(timestamp) {
    return new Intl.DateTimeFormat('en-US', {
        weekday: 'short',
        day: 'numeric',
        month: 'short',
        hour: 'numeric',
        minute: 'numeric',
        hour12: true,
        timeZoneName: 'short',
    }).format(new Date(timestamp));
}

const start_date = document.getElementsByClassName("start_date");

for (const element of start_date) {
    let formatDate = getDateTime(element.innerText);
    element.innerHTML = formatDate;
}