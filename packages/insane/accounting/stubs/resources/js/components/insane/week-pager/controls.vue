<template>
  <div class="pager d-flex">
    <button class="btn btn-primary btn-round mr-2" @click="$emit('prev-week')">
      <i class="fa fa-arrow-left" />
    </button>
    <span class="d-flex justify-content-center align-items-center bolder">
      Semana
    </span>
    <button class="btn btn-primary btn-round ml-2" @click="$emit('next-week')">
      <i  class="fa fa-arrow-right" />
    </button>
  </div>
</template>
