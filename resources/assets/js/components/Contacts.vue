<template>

    <div class="contacts" v-bind:class="{ loading: loading }">

        <form class="uk-form">

            <fieldset data-uk-margin>
                <input v-model="agentAzipCode" type="text" placeholder="Agent A zip code">
                <input v-model="agentBzipCode" type="text" placeholder="Agent B zip code">
                <button class="uk-button uk-button-primary" v-on:click="getMatchedContacts($event)">Match</button>
            </fieldset>

        </form>
    
        
        <div class="table-wrapper">
            <table class="uk-table uk-table-hover uk-table-striped">
                <thead>
                    <tr>
                        <th>Agent</th>
                        <th>Contact name</th>
                        <th>Contact zip code</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="contact in contacts">
                        <td>{{contact.agent.id}}</td>
                        <td>{{contact.contact.name}}</td>
                        <td>{{contact.contact.zipcode}}</td>
                    </tr>
                </tbody>
            </table>
            <div class="loading-layer">
                <div class="background"></div>
                <img src="/img/preloader.gif">
            </div>
        </div>
        
    </div>
        
</template>

<script>
    export default {
        mounted() {
            
        },
        data() {
          return {
            contacts: [],
            agentAzipCode: '',
            agentBzipCode: '',
            loading: false
          }
        },
        methods: {
          getMatchedContacts($event) {
          
            $event.preventDefault();
          
            var $_self = this;
            $_self.loading = true;
            fetch('/api/contact/match-closest/'+this.agentAzipCode+','+this.agentBzipCode, {
                method: 'get'
              }).then(function (response) {
                return response.json();
                $_self.loading = false;
              }).then(function (response) {
                if (response.status == "success") {
                  this.contacts = response.data;
                } else {
                  alert(response.message);
                }
                $_self.loading = false;
              }.bind(this)).catch(function (err) {
                console.error(err);
                $_self.loading = false;
              });
          }
        }
    }
</script>
