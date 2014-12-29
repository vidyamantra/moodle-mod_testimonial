
//  This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * The mod_testimonial course module viewed event.
 *
 * @package mod_testimonial
 * @copyright 2014 Krishna Pratap Singh <krishna@vidyamantra.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

window.onload = function() {
  // Video
  var video = document.getElementById("video");
  //audio
  var audio = document.getElementById("audio");

  // Buttons
  var playButton = document.getElementById("play-pause");
  var muteButton = document.getElementById("mute");
  //var fullScreenButton = document.getElementById("full-screen");

  // Sliders
  var seekBar = document.getElementById("seek-bar");
  var volumeBar = document.getElementById("volume-bar");
  
  var replay = document.getElementById("replay");


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
    volumeBar.value = 0;
    volumeBar.disabled = true;
  } 
  
    else {
    // Unmute the video
    audio.muted=false;
    // Update the button text
    muteButton.value = "Mute";
    volumeBar.value = 50;
    volumeBar.disabled = false;
  }
});

// Event listener for the full-screen button
/*
fullScreenButton.addEventListener("click", function() {
  if (video.requestFullscreen) {
    video.requestFullscreen();
  } else if (video.mozRequestFullScreen) {
    video.mozRequestFullScreen(); // Firefox
  } else if (video.webkitRequestFullscreen) {
    video.webkitRequestFullscreen(); // Chrome and Safari
  }
});*/

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
  
   //make play/pause button to play when testimonial finish
   if(video.duration === video.currentTime){
      playButton.value = "Play";
    }
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

//Event listener for the replay button
replay.addEventListener("click", function() {
  video.currentTime = 0;
  audio.currentTime = 0;
  
    video.play();
    audio.play();
    playButton.value = "Pause";
});

};
