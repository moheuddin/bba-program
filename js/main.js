	import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)

const store = new Vuex.Store({
  state: {
    count: 0
  },
  mutations: {
    increment (state) {
      state.count++
    }
  }
})

	Vue.component('flat-pickr',{
	  props:['value', 'config']
	});
const formData = {
				date: '',
                time: '',
				start_date:'',
				end_date:'',
                description: '',
                comments: '',
                id: 0,
                index: '',
                officer: 0
        };
   const app = new Vue({
        el: '#vue_contacts',
         components:  {
			vuejsDatepicker,'flat-pickr' : VueFlatpickr
		  },
        data: {
				date: null,
                dateTime: '',
                config: {wrap: true,
                enableTime: true,
                noCalendar: true,
                dateFormat: "h:i K",
                defaultDate: null,
                minuteIncrement: "1",
                time_24hr: true
			},
			formData:{
				date: '',
                time: '',
				start_date:'',
				end_date:'',
                description: '',
                comments: '',
                id: 0,
                index: '',
                officer: 0
			},
            time:null,
            fields: [
                { name: ' ', clss: ' ', type: 'none'},
                { name: 'তারিখ', clss: 'fas fa-sort', type: 'string'},
                { name: 'সময়', clss: 'fas fa-sort', type: 'string'},
                { name: 'কার্যক্রম', clss: 'fas fa-sort', type: 'string'},
                { name: 'মন্তব্য', clss: 'fas fa-sort', type: 'string'},
                { name: 'কর্মকর্তা', clss: 'fas fa-sort', type: 'number'},
                { name: ' ', clss: ' ', type: 'none'},
            ],
            asc: 'desc',
            search: '',
            showDisabled: 0,
            labelDisabled: 'Show Disabled',
            contacts: [],
			isLoaded:false,
			isAuthenticate:false,
			userName:'',
            isEditing: false,
            isModalOpen: false,
            modalTitle: 'নতুন কার্যক্রম'
        },
        mounted: function() {
           
		
            let self = this;
            window.addEventListener('keyup', function(event) {
                // If  ESC key was pressed...
                if (event.keyCode === 27) {
                    // try close your dialog
                    self.isModalOpen = false;
                }
            });
            this.getContacts()
			
        },
        computed: {
            
        },
        methods: {
            getBangla:function(english_number){
			var finalEnlishToBanglaNumber={'0':'০','1':'১','2':'২','3':'৩','4':'৪','5':'৫','6':'৬','7':'৭','8':'৮','9':'৯'};
 
				String.prototype.getDigitBanglaFromEnglish = function() {
					var retStr = this;
					for (var x in finalEnlishToBanglaNumber) {
						 retStr = retStr.replace(new RegExp(x, 'g'), finalEnlishToBanglaNumber[x]);
					}
					return retStr;
				};
				 
				return bangla_converted_number=english_number.getDigitBanglaFromEnglish();
 
			},
			dateFormate: function(getDate){
			const date = new Date(getDate)
			const dateTimeFormat = new Intl.DateTimeFormat('en', { year: 'numeric', month: 'short', day: '2-digit' }) 
			const [{ value: month },,{ value: day },,{ value: year }] = dateTimeFormat .formatToParts(date ) 
			console.log(this.getBangla(day));
			return `${day}-${month}-${year }`;
			
			//console.log(`${day}-${month}-${year}`) // just for fun

			},
			dayName: function(dateString){
				var days = ['রবি বার', 'সোমবার', 'মঙ্গলবার', 'বুধবার','বৃহস্পতিবার', 'শুক্রবার', 'শনিবার'];
				var d = new Date(dateString);
				var dayName = days[d.getDay()];
				return dayName
			},
            getContacts: function() {
                axios.get('api/contacts.php')
                    .then(function(response) {
                        
                        app.contacts = response.data.result;
						app.isLoaded = true;
						app.isAuthenticate = response.data.message.isAuthenticate;
						app.userName = response.data.message.isAuthenticate;
						//console.log(response.data.message.isAuthenticate);
						//console.log(response.data.result);
						// Check the response was a success
						console.log(response);
						if(response.data.message === 'success')
						{
							//this.user = response.data.user;
							//app.user_loggedin=0
							
						}
                    })
                    .catch(function(error) {
                        console.log(error);
                    });
            },
            openModal: function() {
                this.isModalOpen = true;
            },
            toggleDisabled: function() {
                this.showDisabled = (this.showDisabled === 1) ? 0 : 1;
                this.labelDisabled = (this.showDisabled === 1) ? 'Show Active' : 'Show Disabled';

            },
            openCreateModal: function() {
                this.resetForm();
                this.openModal();

            },
            closeModal: function() {
                this.isModalOpen = false;
            },
            createContact: function() {
                //console.log("Create contact!")
                var self = this;
				 
				//var formData = this.gatherFormData();
				//console.log(this.formData);
				
                this.openModal();
                let formData = new FormData();
                //console.log("date:", this.formData.date, "date:", this.formData.date)
                formData.append('date', this.formData.date || '')
                formData.append('time', this.formData.time || '')
                formData.append('description', this.formData.description || '')
                formData.append('comments', this.formData.comments || '')
                formData.append('disabled', 0)
                formData.append('officer', this.formData.officer || 0)
				formData.append('insertdata', 1);
                formData.forEach(function(value, key) {
                    //contact[key] = value;
                    //console.log(value);
                });
			
				
                axios({
                        method: 'post',
                        url: 'api/contacts.php',
                        data: formData,
                        config: { headers: { 'Content-Type': 'multipart/form-data' } }
                    })
                    .then(function(response) {
						app.isLoaded = true;
						app.contacts.push({id: 1,date:'12-12-202',time:"12:10",description:'description',comments:'comments',officer:1}); 
						if(response.data.message === 'success')
						{
							//this.user = response.data.user;
							//app.user_loggedin=0
							
						}
                        app.resetForm()
						

                    })
                    .catch(function(response) {
                        //handle error
                        console.log(response)
                    })
                // this.getContacts()
                // this.$forceUpdate()
            },
			searchData: function(){
			
               // this.openModal();
                let formData = new FormData();
                
                formData.append('start_date', this.formData.start_date || '')
                formData.append('end_date', this.formData.end_date || '')
                formData.append('officer', this.formData.officer || 0)
                var contact = {};
                formData.forEach(function(value, key) {
                    //console.log(key );
					contact[key] = value;
                    //console.log(value);
					
                });
				console.log(formData);
                axios({
                        method: 'get',
                        url: 'api/contacts.php',
                        data: formData,
                        config: { headers: { 'Content-Type': 'multipart/form-data' } }
                    })
                    .then(function(response) {
                        //handle success
						
                       app.contacts = response.data.result;
					   console.log(response)
                       
                        //app.resetForm()

                    })
                    .catch(function(response) {
                        //handle error
                        //console.log(response)
                    })
			},
            editContact: function(contact, index) {
                this.formData = contact;
                this.isEditing = true;
                this.modalTitle = 'সম্পাদন';
                this.openModal();
            },
            deleteContact: function(contact, index) {
				 this.formData = contact;
                if (confirm("Are you sure you want to delete, " + contact.id + "?")) {
                    //console.log("Deleting: " + contact.id + " Index: " + index);
                    let formData = new FormData();
                    formData.append('id', contact.id)
                    formData.append('action', 'delete')
                    axios({
                            method: 'post',
                            url: 'api/contacts.php',
                            data: formData,
                            config: { headers: { 'Content-Type': 'multipart/form-data' } }
                        })
                        .then(function(response) {
                            console.log(response)
							this.contacts.splice(contact, contact.id) 
                            contact.disabled = 1;
                            app.resetForm();
                        })
                        .catch(function(response) {
                            //handle error
                            //console.log(response)
                        });
                }
            },
            changeContact: function() {
                console.log("Change contact!")

                let formData = new FormData();
                console.log("date:", this.formData.date, "id:", this.formData.id, " key:", this.formData.index)
                formData.append('id', this.formData.id)
                //formData.append('date', this.formData.date || '')
                //formData.append('time', this.formData.time || '')
                formData.append('description', this.formData.description || '')
                formData.append('comments', this.formData.comments || '')
                formData.append('officer', this.formData.officer || 0)
                var contact = {};
                formData.forEach(function(value, key) {
                    contact[key] = value;
                });

                axios({
                        method: 'post',
                        url: 'api/contacts.php',
                        data: formData,
                        config: { headers: { 'Content-Type': 'multipart/form-data' } }
                    })
                    .then(function(response) {
                        //handle success
                        console.log(response)
                       // Vue.set(app.filteredContacts, app.index, contact)
                        app.resetForm();
                    })
                    .catch(function(response) {
                        //handle error
                        console.log(response)
                    });
            },
            resetForm: function() {
                this.formData = {};
                this.isEditing = false;
                this.closeModal();

            },  
			search_active: function(data) {
                console.log(data)
				axios({
                        method: 'get',
                        url: 'api/contacts.php',
                        data: data,
                        config: { headers: { 'Content-Type': 'multipart/form-data' } }
                    })
                    .then(function(response) {
                        app.contacts = response.data.result;
						app.isLoaded = true;
                       
                    })
                    .catch(function(response) {
                        //handle error
                        console.log(response)
                    });

            },
			
            changecss(){
                    console.log('changecss')
                    this.cssPagedMedia.size = (size)=>{
                        this.cssPagedMedia('@page {size: ' + size + '}');
                    };
                    this.cssPagedMedia.size(this.pagesize);
                },
                printpage(){
                  window.print()        
                }
        }
    });
