//$(function() {    
///   $("#other").click(function() {
 //      $('#result').load('/mod/feedcam/videos.php');
//   });   
//});

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
    
   // alert("paly");
    video.play();
    audio.play();

    // Update the button text to 'Pause'
    playButton.innerHTML = "Pause";
    
  } else {
    // Pause the video
    video.pause();
    audio.pause();
    // Update the button text to 'Play'
    playButton.innerHTML = "Play";
  }
});


// Event listener for the mute button
muteButton.addEventListener("click", function() {
    
   // alert("mute");
  if (video.muted === false && audio.muted === false) {
    // Mute the video
   // video.muted = true;
    audio.muted=true;

    // Update the button text
    muteButton.innerHTML = "Unmute";
  } 
  
    else {
    // Unmute the video
  //  video.muted = false;
    audio.muted=false;

    // Update the button text
    muteButton.innerHTML = "Mute";
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