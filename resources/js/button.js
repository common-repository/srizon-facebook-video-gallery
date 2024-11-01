(function () {
    tinymce.PluginManager.add('srzfb_single_video_button', function (editor, url) {
        editor.addButton('srzfb_single_video_button', {
            text: '',
            icon: 'icon dashicons-video-alt',
            title: 'Srizon Facebook Video',
            type: 'menubutton',
            menu: [
                {
                    text: 'Single Video',
                    onclick: function () {
                        editor.windowManager.open({
                            title: 'Enter Video ID',
                            body: [
                                {
                                    type: 'textbox',
                                    name: 'videoid',
                                    label: 'Video ID'
                                },
                                {
                                    type: 'container',
                                    name: 'container1',
                                    label: 'Video Tutorial',
                                    html: '<a style="color: #3b42a3;" href="https://www.youtube.com/watch?v=No5cg96AH9U" target="_blank">How to find Video ID</a>'
                                }
                            ],
                            onsubmit: function(e){
                                editor.insertContent('[srizonfbvidsingle id='+ e.data.videoid+']');
                            }
                        });
                    }
                },
                {
                    text: 'Video Gallery',
                    onclick: function () {
                        editor.windowManager.open({
                            title: 'Enter Gallery ID',
                            body: [
                                {
                                    type: 'textbox',
                                    name: 'galleryid',
                                    label: 'Gallery ID'
                                },
                                {
                                    type: 'container',
                                    name: 'container2',
                                    label: 'Find it From:',
                                    html: 'Admin Menu >> Facebook Video Galleries'
                                }
                            ],
                            onsubmit: function(e){
                                editor.insertContent('[srizonfbvid id='+ e.data.galleryid+']');
                            }
                        });
                    }
                }
            ]
        });
    });
})();