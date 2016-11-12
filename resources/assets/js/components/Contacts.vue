<template>

    <div class="contacts">

        <button class="btn btn-primary" v-on:click="getMatchedContacts()">Match</button>

        <table>
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
        
    </div>
        
</template>

<script>
    export default {
        mounted() {
            
        },
        data() {
          return {
            contacts: []
          }
        },
        methods: {
          getMatchedContacts() {
            fetch('/api/contact/match-closer/92120,31410', {
                method: 'get'
              }).then(function (response) {
                return response.json();
              }).then(function (response) {
                if (response.status == "success") {
                  this.contacts = response.data;
                } else {
                  console.error(response.message);
                }
              }.bind(this)).catch(function (err) {
                console.error(err);
              });
          }
        }
    }
</script>
