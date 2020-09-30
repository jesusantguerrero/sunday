<template>
    <div class="promodoro-app">
        <header class="bg-red-400 text-white font-bold flex justify-between w-full items-center py-2">
            <span> Promodoro </span>
            <div class="actions rounded-full bg-red-700 flex h-8">
                <button
                  v-for="(modeObj, key) in modes"
                  :key="key"
                  class="px-2 h-full rounded-full"
                  :class="{'bg-red-100 text-red-700': modeSelected == key}"
                  @click="setMode(key)"
                  >
                    {{ modeObj.name }}
                </button>
            </div>
        </header>
        <div class="clock" :class="{ rest: round, ticking: run == 1 }">
            <time class="time" >{{ time | getTime }}</time
            ><span class="note">click here to {{ message }}</span
            ><span >{{ mode }}</span>
            <div class="inner-controls" @click="play">
                <i class="material-icons" >{{ icon }}</i>
            </div>
        </div>
        <div class="outer-controls-container" v-if="false">
            <div class="session-control">
                <h4>Session</h4>
                <div class="cs-row">
                    <button @click="removeTime('session')">
                        <i class="material-icons">navigate_before</i></button
                    ><span class="value" >{{ session }}</span
                    ><button @click="addTime('session')">
                        <i class="material-icons">navigate_next</i>
                    </button>
                </div>
            </div>
            <div class="separator">
                <h4>Reset</h4>
                <div class="cs-row"><button @click="reset">Reset</button></div>
            </div>
            <div class="rest-control">
                <h4>Rest</h4>
                <div class="cs-row">
                    <button @click="removeTime('rest')">
                        <i class="material-icons">navigate_before</i></button
                    ><span class="value" >{{ rest }}</span
                    ><button @click="addTime('rest')">
                        <i class="material-icons">navigate_next</i>
                    </button>
                </div>
                <!-- <audio id="audio" src="./assets/muerte_en_hawaii.mp3"></audio> -->
            </div>
        </div>
    </div>
</template>

<script>
const time= { minutes: 25, seconds: 0}

export default {
    data() {
        return {
            time,
            icon: 'play_arrow',
            run: 0,
            timer: '',
            round: 0,
            audio: '',
            modes: {
                session:{
                    name: 'session',
                    minutes: 25,
                },
                break: {
                    name: 'break',
                    minutes: 5
                },
                longBreak: {
                    name: 'long',
                    minutes: 15
                }
            },
            modeSelected: 'session'
        }
  },
  mounted(){
    this.audio = document.querySelector('#audio')
  },

  filters:{
    getTime(val) {
      let { minutes, seconds } = val
      seconds = (seconds < 10) ? seconds = '0'+ seconds : seconds
      minutes = (minutes < 10) ? minutes = '0' + minutes: minutes
      return `${minutes}:${seconds}`
    }
  },

  computed: {
    mode() {
      return (this.round == 0) ? 'Session' : 'Rest'
    },

    message() {
      switch(this.run){
        case 0:
          return 'start'
        case 1:
          return 'pause'
        case 2:
          return 'resume'
      }
    }
  },

  methods: {
    play() {
      this.stopSound()
      switch (this.run) {
        case 0:
          this.initTimer();
          break
        case 1:
          this.stop()
          break
        case 2:
          this.initTimer('resume')
          break
      }
    },

    stop(){
      clearInterval(this.timer)
      this.run = 2
      this.icon = 'play_arrow'
    },

    reset() {
      this.stop()
      this.run = 0
      this.round = 0
      this.time = {minutes: 25, seconds: 0}
      this.modes.session.minutes = 25
      this.modes.break.minutes = 5
      this.modes.longBreak.minutes = 15
      this.modeSelected = 'session'
    },

    clear(){
      this.stop()
      if (this.round == 0) {
        this.setRestMode()
        this.round = 1
      } else {
        this.round = 0
        this.setSessionMode()
        this.run = 0
      }
    },

    initTimer(selfMode) {
      this.run = 1
      this.icon = 'pause'

      if (!selfMode) {
        this.time.minutes = this.modes[this.modeSelected].minutes
      }

      this.timer = setInterval(()=> {
        this.countDown()
      }, 1000)

    },

    countDown() {
      if (this.time.seconds == 0) {
         this.time.minutes--
         if (this.time.minutes >= 0) {
            this.time.seconds = 59
          } else {
            this.clear()
            this.playSound()
          }
      } else {
        this.time.seconds--
      }
    },

    addTime(property){
      const self = this
      this[property]++

      if (property == 'session' && this.round == 0) {
        self.updateTime(this[property])
      } else if (property == 'rest' && this.round == 1) {
        self.updateTime(this[property])
      }

      this.stop()
    },

    removeTime(property){
      const self = this
      if (this[property] > 0) {
        this[property]--
      }

      if (property == 'session' && this.round == 0) {
        self.updateTime(this[property])
      } else if (property == 'rest' && this.round == 1) {
        self.updateTime(this[property])
      }

      this.stop()
    },

    updateTime(mins, secs = "00"){
      this.time.minutes = mins;
      this.time.seconds = secs;
    },

    playSound(){
    //   this.audio.currentTime = 0
    //   this.audio.play()
      window.navigator.vibrate([1000, 100, 1000, 100, 1000, 100, 1000])
      const stop = confirm(`the time of the ${this.modeSelected} has finished`)
    //   this.stopSound()
    },

    stopSound() {
        //   this.audio.pause()
      window.navigator.vibrate(0)
    },

    setMode(modeName) {
      this.modeSelected = modeName;
      this.time.minutes = this.modes[modeName].minutes;
      this.time.seconds = 0;
    }
  }
};
</script>

<style lang="scss" scoped>
.promodoro-app {
	display: flex;
	flex-direction: column;
	justify-content: center;
	align-items: center;
	width: 100%;
	height: 100%;
	text-align: center;
}

.clock {
    @apply py-5;
	background: transparent;
	min-height: 200px;
	width: 100%;
	color: #fff;
	display: flex;
	flex-direction: column;
	justify-content: center;
	align-items: center;
	cursor: pointer;
    position: relative;
}

.clock:hover {
	box-shadow: 3px 4px 5px rgba(0, 0, 0, 0.2);
}

.clock:hover .inner-controls {
	opacity: 7;
}


.clock:before {
	content: '';
	position: absolute;
	width: 100%;
	height: 100%;
	background: rgba(255, 255, 255, 0.3);
	transition: all ease 1s;
	display: flex;
	flex-direction: row;
	justify-content: center;
	align-items: center;
	z-index: -1;
}

.clock.ticking:before {
	animation: ticking 3s infinite;
}

.title {
	margin: 0;
}

.time {
	font-size: 90px;
}

.inner-controls {
	position: absolute;
	width: 100%;
	height: 100%;
	background: rgba(0, 0, 0, 0.5);
	opacity: 0;
	transition: all ease 1s;
	display: flex;
	flex-direction: row;
	justify-content: center;
	align-items: center;
}

.inner-controls .material-icons {
	font-size: 150px;
}

.outer-controls-container {
	display: flex;
	flex-direction: row;
	justify-content: space-around;
	align-items: center;
}

.outer-controls-container [class*="-control"] {
	margin: 5px;
}

.cs-row {
	width: 100%;
	height: 48px;
	display: flex;
	flex-direction: row;
	justify-content: center;
	align-items: center;
}

.cs-row button {
	border: 0;
	height: 100%;
	color: white;
	padding: 5px;
	background: #0277BD;
	display: flex;
	flex-direction: row;
	justify-content: center;
	align-items: center;
	display: inline-flex;
	cursor: pointer;
}

.cs-row button.reset-btn {
	margin: 5px 0;
}

.cs-row button:hover {
	background: #0397ef;
}

.cs-row .value {
	display: inline-block;
	height: 100%;
	padding: 0 10px;
	display: flex;
	flex-direction: row;
	justify-content: center;
	align-items: center;
	background: #0277BD;
}

@keyframes ticking {
	0% {
		transform: scale(1);
	}

	30% {
		transform: scale(1.1);
	}

	70% {
		transform: scale(1.1);
		border: 2px solid rgba(255, 255, 255, 0.3);
		background: transparent;
	}

	100% {
		transform: scale(1);
	}
}
</style>
