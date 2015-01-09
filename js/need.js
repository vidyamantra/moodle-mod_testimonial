
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
  
  var replayvid = document.getElementById("replayvid");
  var currenttime = document.getElementById("currenttime");
  var durationtime = document.getElementById("durationtime");
  seekBar.value=0;
  

// Event listener for the play/pause button
playButton.addEventListener("click", function() {
   if(audio===null){
       alert('no audio');
   }
  
    if (video.paused === true && audio.paused === true) {
        
       if(video.duration === video.currentTime){
         video.load();
         audio.load();
       }  
    // Play the video
    video.play();
    audio.play();
    // Update the button text to 'Pause'
    playButton.value = "Pause";
     audio.currentTime = video.currentTime;
    durationtime.value = video.currentTime;
    } 
  else {
    // Pause the video
    video.pause();
    audio.pause();
    // Update the button text to 'Play'
    playButton.value = "Play";
  
      audio.currentTime = video.currentTime;
    durationtime.value = video.currentTime;
  }
});



// Event listener for the mute button
muteButton.addEventListener("click", function() {
    
  if (video.muted === false && audio.muted === false) {
    // Mute the video
    
        audio.muted=true;
    
        video.muted=true;
    // Update the button text
    muteButton.value = "Unmute";
    volumeBar.value = 0;
    volumeBar.disabled = true;
  } 
  
    else {
    // Unmute the video
    
        audio.muted=false;
    
        video.muted=false;
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
//  var timea = audio.duration * (seekBar.value / 100);
  // Update the video time
  video.currentTime = time;
  audio.currentTime = video.currentTime;
  
  if (video.paused === true && audio.paused === true) {
    playButton.value = "Play";
  } else {
    playButton.value = "Pause";
  }
  
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
   audio.currentTime = video.currentTime;
  
  var curmins = Math.floor(video.currentTime / 60);
  var cursecs = Math.floor(video.currentTime - curmins * 60);
  var durmins = Math.floor(video.duration / 60); 
  var dursecs = Math.floor(video.duration - durmins * 60);
  if(cursecs < 10){ 
      cursecs = "0"+cursecs;
  }
  if(dursecs < 10){ 
      dursecs = "0"+dursecs;
  }
  if(curmins < 10){
      curmins = "0"+curmins;
  }
  if(durmins < 10){
      durmins = "0"+durmins; 
  } 
  currenttime.innerHTML = curmins+":"+cursecs;

  durationtime.innerHTML = "|"+durmins+":"+dursecs;
  
});


// Pause the video when the slider handle is being dragged
seekBar.addEventListener("mousedown", function() {
  video.pause();
  audio.pause();
  audio.currentTime = video.currentTime;
});


// Play the video when the slider handle is dropped
seekBar.addEventListener("mouseup", function() {
  video.play();
  audio.play();
  audio.currentTime = video.currentTime;
});


// Event listener for the volume bar
volumeBar.addEventListener("change", function() {
  // Update the video volume
  video.volume = volumeBar.value;
  audio.volume=volumeBar.value;
});

//Event listener for the replay button
replayvid.addEventListener("click", function() {
    
    //alert('done');
    seekBar.value=0;
    video.load();
    audio.load();
  
    video.play();
    audio.play();
    playButton.value = "Pause";
});

};
