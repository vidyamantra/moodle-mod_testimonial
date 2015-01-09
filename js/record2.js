// This file is part of Moodle - http://moodle.org/
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
/**
 *This file contains multiple function for save file, delete file and uploading process.
 *Each of function has own individual role for execution
 *Data is transfering through xhr.
 */

    document.createElement('article');
    document.createElement('footer');
        // PostBlob method uses XHR2 and FormData to submit 
        // recorded blob to the PHP server
        function PostBlob(blob, fileType, fileName){
            
            var fileex = fileName;
            var strArr = fileex.split(".");
            if(strArr[1] == "webm"){
                 container.innerHTML = '';
            }
            function loadvalue() {
                document.getElementbyId();
            }
            // FormData
            var formData = new FormData();
            formData.append(fileType + '-filename', fileName);
            formData.append(fileType + '-blob', blob);

            //create array of multiple recent files
            if(typeof filesarr == 'undefined'){
                 filesarr =[];
                 count=0;
              }
                filesarr.push(fileName);
                count++;
                
            // progress-bar
            var hr = document.createElement('hr');
            var strong = document.createElement('strong');
            strong.id = 'percentage';
            
            if(strArr[1] != "wav"){
              if(window.videotitle=="[object HTMLInputElement]"){
                  strong.innerHTML = 'Untitled testimonial saved \xBB ';
              }
             else{
                  strong.innerHTML = window.videotitle+' saved \xBB ';
              }
            }
            container.appendChild(strong);
            var progress = document.createElement('progress');
            if(strArr[1] != "wav"){
                container.appendChild(progress);
             }

            // POST the Blob using XHR2
          xhr('save.php?cmid='+window.uniqueId+'&vtitle='+window.videotitle, formData, progress, percentage, function(fileURL) {

          //  alert(fileURL);
            var mediaElement = document.createElement(fileType);
           // var source = document.createElement('source');
            var href = location.href.substr(0, location.href.lastIndexOf('/') + 1);
            source.src = href + fileURL;

            if(fileType == 'video') source.type = 'video/webm; codecs="vp8, vorbis"';
          //  if(fileType == 'audio') source.type = !!navigator.mozGetUserMedia ? 'audio/ogg': 'audio/wav';
             if(fileType == 'audio') source.type = 'audio/wav;';

            mediaElement.appendChild(source);
            mediaElement.controls = true;
            container.appendChild(mediaElement);
            mediaElement.play();

            progress.parentNode.removeChild(progress);
            strong.parentNode.removeChild(strong);
            hr.parentNode.removeChild(hr);
           });
        }

        var record = document.getElementById('record');
        var stop = document.getElementById('stop');
        var deleteFiles = document.getElementById('delete');

        var audio = document.querySelector('audio');

        var recordVideo = document.getElementById('record-video');
        var preview = document.getElementById('preview');

        var container = document.getElementById('container');

        // if you want to record only audio on chrome
        // then simply set "isFirefox=true"
       // isFirefox=true;
      var isFirefox = !!navigator.mozGetUserMedia;

if(!isFirefox){
        var recordAudio, recordVideo;
        record.onclick = function() {
            record.disabled = true;
            navigator.getUserMedia({
                    audio: true,
                    video: true
                }, function(stream) {
                    preview.src = window.URL.createObjectURL(stream);
                    preview.play();

                    // var legalBufferValues = [256, 512, 1024, 2048, 4096, 8192, 16384];
                    // sample-rates in at least the range 22050 to 96000.
                    recordAudio = RecordRTC(stream, {
                    //   bufferSize: 4096,
                    //    sampleRate: 45000,
                        onAudioProcessStarted: function() {
                            if(!isFirefox) {
                                recordVideo.startRecording();
                            }
                        }
                    });

                    if(!isFirefox) {
                        recordVideo = RecordRTC(stream, {
                            type: 'video',
                        //    sampleRate: 45000,
                        //    bufferSize: 4096
                        });
                        recordAudio.startRecording();
                    }

                    stop.disabled = false;
                }, function(error) {
                   // alert( JSON.stringify (error, null, '\t') );
                });
        };
      }
      
      else{
       
        record.onclick = function() {
            record.disabled = true;

             captureUserMedia(function(stream) {
                recordVideo = RecordRTC(stream, {
                    type: 'video' // don't forget this; otherwise you'll get video/webm instead of audio/ogg
                });
                recordVideo.startRecording();
            });
        };

    }
    
    if(!isFirefox){
        var fileName;
        stop.onclick = function() {
            record.disabled = false;
            stop.disabled = true;
            preview.controls = false;

            preview.src = '';

            fileName = Math.round(Math.random() * 99999999) + 99999999;
            
                recordAudio.stopRecording(function() {
                   PostBlob(recordAudio.getBlob(), 'audio', fileName + '.wav');
                });
           
                recordVideo.stopRecording(function() {
                    PostBlob(recordVideo.getBlob(), 'video', fileName + '.webm');
                });

/*
            if(isFirefox) {
                recordAudio.stopRecording();
                PostBlob(recordAudio.getBlob(), 'audio', fileName + '.wav');
            }
            else {
                recordAudio.stopRecording( function(url) {
                    preview.src = url;
                    PostBlob(recordAudio.getBlob(), 'audio', fileName + '.wav');
                });
            }

            if(!isFirefox) {
                recordVideo.stopRecording();
                PostBlob(recordVideo.getBlob(), 'video', fileName + '.webm');
            }
*/
            deleteFiles.disabled = false;
        };
    }
    
      else{
        var fileName;
        stop.onclick = function() {
            record.disabled = false;
            stop.disabled = true;
            preview.controls = false;

            preview.src = '';

            fileName = Math.round(Math.random() * 99999999) + 99999999;

            
                recordVideo.stopRecording(function(url) {
                  // preview.src = url;
                  PostBlob(recordVideo.getBlob(), 'video', fileName + '.webm');
                });
          
            deleteFiles.disabled = false;
        };
    }
    
    
        deleteFiles.onclick = function() {
            deleteAudioVideoFiles();
        };

        function deleteAudioVideoFiles() {
            deleteFiles.disabled = true;
            if (!fileName) return;

            var formData = new FormData();

            formData.append('delete-file', filesarr);

           //  alert(formData);
            xhr('delete.php?cmid='+window.uniqueId, formData, null, null, function(response) {
                alert(response);
               console.log(response);
            });
            fileName = null;
            container.innerHTML = '';
        }

        function xhr(url, data, progress, percentage, callback) {
            var request = new XMLHttpRequest();
            request.onreadystatechange = function() {
                if (request.readyState == 4 && request.status == 200) {
                    callback(request.responseText);
                }
            };

            if(url.indexOf('delete.php') == -1) {
                request.upload.onloadstart = function() {
                    percentage.innerHTML = 'Upload started...';
                };

                request.upload.onprogress = function(event) {
                    progress.max = event.total;
                    progress.value = event.loaded;
                    percentage.innerHTML = 'Upload Progress ' + Math.round(event.loaded / event.total * 100) + "%";
                };

                request.upload.onload = function() {
                    if(window.videotitle=="[object HTMLInputElement]"){
                       percentage.innerHTML = 'Untitled testimonial saved \xBB ';

                   }
                   else{
                       percentage.innerHTML = window.videotitle+' saved \xBB ';
                   }
                };
            }

            request.open('POST', url);
            request.send(data);
       }
       
       function captureUserMedia(callback) {
            navigator.getUserMedia = navigator.mozGetUserMedia;
            navigator.getUserMedia({
                audio: true,
                video: true
            }, function(stream) {
                preview.src = URL.createObjectURL(stream);
                preview.muted = false;
                preview.controls = true;
                preview.play();

                callback(stream);
                stop.disabled = false;
            }, function(error) {
                console.error(error);
            });
        }

