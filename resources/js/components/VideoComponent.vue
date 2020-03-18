<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">

                    <div class="card-body">
                        <div class="App">
                            <div class="video-container">
                                <video class="my-video" ref="me"></video>
                                <video class="user-video" ref="me"></video>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                hasMedia: false,
                otherUserId: null
            }
        },
        mounted() {
            navigator.mediaDevices.getUserMedia({
                video: true,
                audio: true
            }).then(stream=>{
                console.log(stream);
                this.hasMedia = true;
                try{
                    this.$ref.me.srcObject = stream;
                } catch {
                     this.$ref.me.srcObject = URL.createObjectURL(stream);
                }

                this.$ref.me.play();

            }).catch(err=>{
                throw new Error('Unable to fetch stream '+err);
            });
        }
    }
</script>
