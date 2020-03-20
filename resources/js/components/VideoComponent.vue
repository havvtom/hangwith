<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">

                    <div class="card-body">
                        <button  @click="callTo(2)">CallUser2</button>
                        <div class="App">
                            <div class="video-container">
                                 
                                <video class="my-video" ref="me"></video>
                                <video class="user-video" ref="user"></video>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import Peer from 'simple-peer'
    export default {
        props: ['user'],
        data() {
            return {
                hasMedia: false,
                otherUserId: null,
                userStream: null,
                peers: {},
                channel: ''
            }
        },
        methods: {
            setupPusher() {
                // Pusher.logToConsole = true;
                var pusher = new Pusher("6e5bc172c442bd95cee1", {
                    authEndpoint: '/pusher/auth',
                    cluster: 'mt1',
                    auth: {
                        params: this.user.id,
                        headers: {
                            'X-CSRF-Token': window.csrfToken
                        }
                    }
                });

                this.channel = pusher.subscribe('presence-video-channel');

                this.channel.bind('client-signal-'+this.user.id, signal=>{
                    
                    let peer = this.peers[signal.userId];

                    if(peer === undefined){
                        this.otherUserId = signal.userId;
                        peer = this.startPeer(signal.userId, false);
                    }
                    console.log("peer", signal);
                    peer.signal(signal.data);
                });
            },
            startPeer(userId, initiator = true){
                var peer = new Peer({
                    initiator,
                    stream: this.userStream,
                    trickle: false
                });

                peer.on('signal', data=>{
                    console.log(data)
                    this.channel.trigger("client-signal-"+userId, {
                        userId:this.user.id,
                        type: 'signal',
                        data: data
                    });
                });

                peer.on('stream', stream=>{
                    try{
                    this.$refs.user.srcObject = stream;
                    } catch {
                         this.$refs.user.srcObject = URL.createObjectURL(stream);
                    }
                   this.$refs.user.play(); 
                });

                peer.on('close', ()=>{

                    let peer = this.peers[userId];
                    if(peer !== undefined) {
                        peer.destroy();
                    }

                    this.peers[userId] = undefined;
                        });

                return peer;
            },
            callTo(userId) {
                this.peers[userId] = this.startPeer(userId);;
            }
        },
        mounted() {
            navigator.mediaDevices.getUserMedia({
                video: true,
                audio: true
            }).then(stream=>{
                
                this.userStream = stream;
                this.hasMedia = true;
                try{
                    this.$refs.me.srcObject = stream;
                } catch {
                     this.$refs.me.srcObject = URL.createObjectURL(stream);
                }

                this.$refs.me.play();

            }).catch(err=>{
                throw new Error('Unable to fetch stream '+err);
            });
        },
        created(){
            this.setupPusher();
        }
    }
</script>

