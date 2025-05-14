const { createApp } = Vue;

createApp({
    data() {
        return {
            leads: [],
            filteredLeads: [],
            filters: {
                platform: '',
                status: '',
                search: '',
                sortBy: 'newest'
            },
            stats: {
                total: 0,
                new: 0,
                followUp: 0,
                converted: 0
            },
            platforms: [
                { value: 'linkedin.com', label: 'LinkedIn' },
                { value: 'x.com', label: 'X (Twitter)' },
                { value: 'facebook.com', label: 'Facebook' },
                { value: 'instagram.com', label: 'Instagram' },
                { value: 'tiktok.com', label: 'TikTok' }
            ],
            alert: {
                show: false,
                message: '',
                type: 'success',
                timeout: null
            },
            currentLead: {
                id: null,
                company_name: '',
                niche: '',
                location: '',
                contact_person: '',
                email: '',
                phone: '',
                status: 'new',
                notes: '',
                platform: '',
                url: ''
            }
        };
    },
    computed: {
        alertIcon() {
            return {
                'fa-check-circle': this.alert.type === 'success',
                'fa-exclamation-circle': this.alert.type === 'danger',
                'fa-exclamation-triangle': this.alert.type === 'warning'
            };
        }
    },
    methods: {
        async loadLeads() {
            try {
                const response = await fetch('api/leads.php?source=database');
                const data = await response.json();
                
                if (data.leads) {
                    this.leads = data.leads;
                    this.updateStats();
                    this.filterLeads();
                }
            } catch (error) {
                console.error('Error loading leads:', error);
                this.showAlert('Error loading leads: ' + error.message, 'danger');
            }
        },
        filterLeads() {
            let filtered = [...this.leads];

            // Filter by platform
            if (this.filters.platform) {
                filtered = filtered.filter(lead => lead.platform === this.filters.platform);
            }

            // Filter by status
            if (this.filters.status) {
                filtered = filtered.filter(lead => lead.status === this.filters.status);
            }

            // Filter by search term
            if (this.filters.search) {
                const searchTerm = this.filters.search.toLowerCase();
                filtered = filtered.filter(lead => 
                    lead.company_name.toLowerCase().includes(searchTerm) ||
                    lead.location.toLowerCase().includes(searchTerm) ||
                    lead.niche.toLowerCase().includes(searchTerm) ||
                    (lead.contact_person && lead.contact_person.toLowerCase().includes(searchTerm)) ||
                    (lead.email && lead.email.toLowerCase().includes(searchTerm))
                );
            }

            // Sort leads
            filtered.sort((a, b) => {
                switch (this.filters.sortBy) {
                    case 'newest':
                        return new Date(b.created_at) - new Date(a.created_at);
                    case 'oldest':
                        return new Date(a.created_at) - new Date(b.created_at);
                    case 'company':
                        return a.company_name.localeCompare(b.company_name);
                    case 'status':
                        return a.status.localeCompare(b.status);
                    default:
                        return 0;
                }
            });

            this.filteredLeads = filtered;
        },
        updateStats() {
            this.stats = {
                total: this.leads.length,
                new: this.leads.filter(lead => lead.status === 'new').length,
                followUp: this.leads.filter(lead => lead.status === 'follow_up').length,
                converted: this.leads.filter(lead => lead.status === 'converted').length
            };
        },
        async updateLeadStatus(lead) {
            try {
                const response = await fetch(`api/leads.php?id=${lead.id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ status: lead.status })
                });

                if (response.ok) {
                    this.showAlert('Lead status updated successfully', 'success');
                    this.updateStats();
                } else {
                    throw new Error('Failed to update lead status');
                }
            } catch (error) {
                console.error('Error updating lead status:', error);
                this.showAlert('Error updating lead status: ' + error.message, 'danger');
            }
        },
        async deleteLead(id) {
            if (!confirm('Are you sure you want to delete this lead?')) {
                return;
            }

            try {
                const response = await fetch(`api/leads.php?id=${id}`, {
                    method: 'DELETE'
                });

                if (response.ok) {
                    this.leads = this.leads.filter(lead => lead.id !== id);
                    this.filterLeads();
                    this.updateStats();
                    this.showAlert('Lead deleted successfully', 'success');
                } else {
                    throw new Error('Failed to delete lead');
                }
            } catch (error) {
                console.error('Error deleting lead:', error);
                this.showAlert('Error deleting lead: ' + error.message, 'danger');
            }
        },
        getPlatformIcon(platform) {
            const icons = {
                'linkedin.com': 'fa-linkedin',
                'x.com': 'fa-twitter',
                'facebook.com': 'fa-facebook',
                'instagram.com': 'fa-instagram',
                'tiktok.com': 'fa-tiktok'
            };
            return icons[platform] || 'fa-globe';
        },
        getPlatformClass(platform) {
            return platform ? platform.split('.')[0] : 'default';
        },
        showAlert(message, type = 'success') {
            if (this.alert.timeout) {
                clearTimeout(this.alert.timeout);
            }
            this.alert.show = true;
            this.alert.message = message;
            this.alert.type = type;
            this.alert.timeout = setTimeout(() => {
                this.alert.show = false;
            }, 5000);
        },
        editLead(lead) {
            this.currentLead = { ...lead };
        },
        async saveLead() {
            try {
                const response = await fetch(`api/leads.php?id=${this.currentLead.id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(this.currentLead)
                });

                if (response.ok) {
                    // Update the lead in the local array
                    const index = this.leads.findIndex(l => l.id === this.currentLead.id);
                    if (index !== -1) {
                        this.leads[index] = { ...this.currentLead };
                    }
                    
                    // Close modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('editLeadModal'));
                    modal.hide();
                    
                    // Update filtered leads and stats
                    this.filterLeads();
                    this.updateStats();
                    
                    this.showAlert('Lead updated successfully', 'success');
                } else {
                    throw new Error('Failed to update lead');
                }
            } catch (error) {
                console.error('Error updating lead:', error);
                this.showAlert('Error updating lead: ' + error.message, 'danger');
            }
        }
    },
    mounted() {
        this.loadLeads();
    }
}).mount('#app');
