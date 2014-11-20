
window.onload = function() {
  // Video
  var video = document.getElementById("video");
  //audio
  var audio = document.getElementById("audio");

  // Buttons
  var playButton = document.getElementById("play-pause");
  var muteButton = document.getElementById("mute");
  var fullScreenButton = document.getElementById("full-screen");

  // Sliders
  var seekBar = document.getElementById("seek-bar");
  var volumeBar = document.getElementById("volume-bar");


// Event listener for the play/pause button
playButton.addEventListener("click", function() {
  
    if (video.paused === true && audio.paused === true) {
    // Play the video
    
    video.play();
    audio.play();
    // Update the button text to 'Pause'
    playButton.value = "Pause";
    
  } else {
    // Pause the video
    video.pause();
    audio.pause();
    // Update the button text to 'Play'
    playButton.value = "Play";
  }
});


// Event listener for the mute button
muteButton.addEventListener("click", function() {
    
  if (video.muted === false && audio.muted === false) {
    // Mute the video
    audio.muted=true;

    // Update the button text
    muteButton.value = "Unmute";
  } 
  
    else {
    // Unmute the video
    audio.muted=false;

    // Update the button text
    muteButton.value = "Mute";
  }
});

// Event listener for the full-screen button
fullScreenButton.addEventListener("click", function() {
  if (video.requestFullscreen) {
    video.requestFullscreen();
  } else if (video.mozRequestFullScreen) {
    video.mozRequestFullScreen(); // Firefox
  } else if (video.webkitRequestFullscreen) {
    video.webkitRequestFullscreen(); // Chrome and Safari
  }
});

// Event listener for the seek bar
seekBar.addEventListener("change", function() {
  // Calculate the new time
  var time = video.duration * (seekBar.value / 100);
  var timea = audio.duration * (seekBar.value / 100);

  // Update the video time
  video.currentTime = time;
  audio.currentTime = timea;
});

// Update the seek bar as the video plays
video.addEventListener("timeupdate", function() {
  // Calculate the slider value
  var value = (100 / video.duration) * video.currentTime;

  // Update the slider value
  seekBar.value = value;
});

// Pause the video when the slider handle is being dragged
seekBar.addEventListener("mousedown", function() {
  video.pause();
  audio.pause();
});

// Play the video when the slider handle is dropped
seekBar.addEventListener("mouseup", function() {
  video.play();
  audio.play();
});


// Event listener for the volume bar
volumeBar.addEventListener("change", function() {
  // Update the video volume
  video.volume = volumeBar.value;
  audio.volume=volumeBar.value;
});

}