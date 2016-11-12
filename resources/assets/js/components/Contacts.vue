<template>

    <div class="contacts" v-bind:class="{ loading: loading }">

        <form class="uk-form">

            <fieldset data-uk-margin>
                <input v-model="agentAzipCode" type="text" placeholder="Agent A zip code">
                <input v-model="agentBzipCode" type="text" placeholder="Agent B zip code">
                <button class="uk-button uk-button-primary" v-on:click="getMatchedContacts($event)">Match</button>
            </fieldset>

        </form>
        
        <div id="map"></div>    
        
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
                  this.contacts = response.data.contacts;                  
                  this.drawMap(response.data.contacts, response.data.agents);
                } else {
                  alert(response.message);
                }
                $_self.loading = false;
              }.bind(this)).catch(function (err) {
                console.error(err);
                $_self.loading = false;
              });
          },
          drawMap(contacts, agents) {
            
            // Get an approximate center by calculating the average
            var avglat = 0; var avglng = 0;
            var n = contacts.length;
            var m = 2; //agents.length;
            if(n > 0) {
                for(var i in contacts) {
                    var contact = contacts[i];
                    avglat += parseFloat(contact.location.latitude);
                    avglng += parseFloat(contact.location.longitude);
                }
                for(var i in agents) {
                    var agent = agents[i];
                    avglat += parseFloat(agent.location.latitude);
                    avglng += parseFloat(agent.location.longitude);
                }
                avglat = avglat / (m+n);
                avglng = avglng / (m+n);
            }
            

            var map = new google.maps.Map(document.getElementById('map'), {
              zoom: 4,
              center: {lat: avglat, lng: avglng}
            });

            var markers = [];
            for(var i in contacts) {
                var contact = contacts[i];
                markers.push( new google.maps.Marker({
                  position: {
                    lat:parseFloat(contact.location.latitude),
                    lng:parseFloat(contact.location.longitude)
                  },
                  map: map,
                  title: contact.contact.name,
                  label: contact.agent.id
                }) );
            }
            
            var image = '/img/agent-marker.png';
            for(var i in agents) {
                var agent = agents[i];
                markers.push( new google.maps.Marker({
                  position: {
                    lat:parseFloat(agent.location.latitude),
                    lng:parseFloat(agent.location.longitude)
                  },
                  map: map,
                  icon: image,
                  title: agent.id,
                  label: 'Agent-'+agent.id
                }) );
            }
            
          }
        }
    }
</script>
