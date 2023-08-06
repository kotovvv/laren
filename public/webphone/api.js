webphone_api.onCdr(function (caller, called, connecttime, duration, direction, peerdisplayname, reason) {
  fetch('/api/onCdr?q=" + caller + "&w=" + called + "&z=" + duration + "&x=" + reason' + "&t=" + connecttime')
    .then(response => response.json())
    .then(console.log(data))
})
