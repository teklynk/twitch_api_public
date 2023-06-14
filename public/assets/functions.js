function getUrlParameter(name) {
    name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
    let regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
    let results = regex.exec(location.search);
    return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
}

let format = getUrlParameter('format').toLowerCase().trim();

// Format date/time to locale
function getDateTime(timestamp) {
    return new Intl.DateTimeFormat('en-US', {
        weekday: 'short',
        day: 'numeric',
        month: 'short',
        hour: 'numeric',
        minute: 'numeric',
        hour12: true,
        timeZoneName: 'short'
    }).format(new Date(timestamp));
}

const start_date = document.getElementsByClassName("start_date");

console.log(format);

for (const dateEl of start_date) {
    let formatDate = getDateTime(dateEl.innerText);
    if (format == '1') {
        formatDate = formatDate.replace(', ', ' | '); // first occurance
        formatDate = formatDate.replace(', ', ' | '); // second occurance
        formatDate = formatDate.replace(' PM', 'PM');
        formatDate = formatDate.replace(' AM', 'AM');
        formatDate = formatDate.replace(':00', '');
        formatDate = formatDate.replace('EDT', '');
        formatDate = formatDate.replace('PDT', '');
        formatDate = formatDate.replace('MDT', '');
    }
    dateEl.innerText = formatDate;
}