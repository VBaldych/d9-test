const { createApp } = Vue

createApp({
  data() {
    return {
      hello: 'Hello World, Again!',
      names: [
        { firstname: 'John', lastname:'Doe' },
        { firstname: 'Jane', lastname:'Jones' },
        { firstname: 'Will ', lastname:'Smith' }
      ]
    }
  }
}).mount('#app');
