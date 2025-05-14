const { createApp } = Vue

createApp({
    data() {
        return {
            leads: [],
            currentLead: {
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
            },
            filters: {
                niche: '',
                location: '',
                source: 'google',
                platform: 'linkedin.com',
                query: ''
            },
            platforms: [
                { value: 'linkedin.com', label: 'LinkedIn' },
                { value: 'x.com', label: 'X (Twitter)' },
                { value: 'facebook.com', label: 'Facebook' },
                { value: 'instagram.com', label: 'Instagram' },
                { value: 'tiktok.com', label: 'TikTok' }
            ],
            niches: [
                // Professional Services
                { category: 'Professional Services', items: [
                    'Real Estate', 'Law Firm', 'Accounting', 'Consulting', 'Financial Services',
                    'Insurance', 'Business Services', 'Marketing Agency', 'PR Agency', 'IT Services'
                ]},
                // Healthcare
                { category: 'Healthcare', items: [
                    'Medical Practice', 'Dental', 'Healthcare Services', 'Mental Health',
                    'Physical Therapy', 'Wellness Center', 'Alternative Medicine', 'Nutrition'
                ]},
                // Retail & E-commerce
                { category: 'Retail & E-commerce', items: [
                    'Fashion', 'Electronics', 'Home Goods', 'Jewelry', 'Sports Equipment',
                    'Beauty Products', 'Pet Supplies', 'Toys & Games', 'Books & Stationery'
                ]},
                // Food & Hospitality
                { category: 'Food & Hospitality', items: [
                    'Restaurant', 'Cafe', 'Catering', 'Hotel', 'Travel Agency',
                    'Food Service', 'Bakery', 'Brewery', 'Wine & Spirits'
                ]},
                // Construction & Home Services
                { category: 'Construction & Home Services', items: [
                    'Construction', 'Architecture', 'Interior Design', 'Landscaping',
                    'Home Renovation', 'Plumbing', 'Electrical', 'HVAC', 'Cleaning Services'
                ]},
                // Education & Training
                { category: 'Education & Training', items: [
                    'Education', 'Training Services', 'Online Courses', 'Tutoring',
                    'Professional Development', 'Language School', 'Music School', 'Art School'
                ]},
                // Technology
                { category: 'Technology', items: [
                    'Software Development', 'Web Development', 'App Development', 'Cybersecurity',
                    'Cloud Services', 'AI & Machine Learning', 'Data Analytics', 'IoT Solutions'
                ]},
                // Automotive
                { category: 'Automotive', items: [
                    'Auto Repair', 'Car Dealership', 'Auto Parts', 'Car Rental',
                    'Car Wash', 'Auto Body Shop', 'Motorcycle', 'Fleet Services'
                ]},
                // Health & Beauty
                { category: 'Health & Beauty', items: [
                    'Salon', 'Spa', 'Barbershop', 'Cosmetics', 'Fitness Center',
                    'Yoga Studio', 'Personal Training', 'Beauty Supply'
                ]},
                // Creative & Media
                { category: 'Creative & Media', items: [
                    'Photography', 'Video Production', 'Graphic Design', 'Digital Media',
                    'Advertising', 'Music Production', 'Event Planning', 'Art Gallery'
                ]},
                // Manufacturing
                { category: 'Manufacturing', items: [
                    'Manufacturing', 'Industrial Equipment', 'Textile', 'Food Production',
                    'Metal Fabrication', 'Plastics', 'Electronics Manufacturing'
                ]},
                // Environmental
                { category: 'Environmental', items: [
                    'Renewable Energy', 'Recycling', 'Environmental Services',
                    'Green Technology', 'Sustainable Products', 'Waste Management'
                ]}
            ],
            pagination: {
                currentPage: 1,
                totalPages: 1,
                totalItems: 0,
                perPage: 10
            },
            isEditing: false,
            loading: false,
            error: null,
            alert: {
                show: false,
                message: '',
                type: 'success', // success, danger, warning
                timeout: null
            }
        }
    },
    methods: {
        getStatusColor(status) {
            const colors = {
                'new': 'info',
                'contacted': 'primary',
                'follow_up': 'warning',
                'converted': 'success',
                'not_interested': 'danger'
            };
            return colors[status] || 'secondary';
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
            return platform.split('.')[0];
        },
        buildSearchQuery() {
            const platform = this.filters.platform;
            const niche = this.filters.niche;
            const location = this.filters.location;
            
            // Platform-specific query patterns
            const queryPatterns = {
                'linkedin.com': () => {
                    const parts = [
                        `site:${platform}/company`,
                        `"${niche}"`,
                        location && `"${location}"`,
                        '("email" OR "contact us")',
                        '("business hours" OR "about us")',
                        '-"profile"',
                        '-"personal"'
                    ].filter(Boolean);
                    return parts.join(' ');
                },
                'x.com': () => {
                    const parts = [
                        `site:${platform}`,
                        `"business"`,
                        `"${niche}"`,
                        location && `"${location}"`,
                        '("official" OR "company" OR "business")',
                        '("email" OR "contact")',
                        '-"personal"',
                        '-"profile"',
                        'bio'
                    ].filter(Boolean);
                    return parts.join(' ');
                },
                'facebook.com': () => {
                    const parts = [
                        `site:${platform}/pages`,
                        `"${niche}"`,
                        location && `"${location}"`,
                        '("business" OR "company")',
                        '("email" OR "contact us")',
                        '("about" OR "business hours")',
                        '-"personal"',
                        '-"profile"'
                    ].filter(Boolean);
                    return parts.join(' ');
                },
                'instagram.com': () => {
                    const parts = [
                        `site:${platform}`,
                        `"business"`,
                        `"${niche}"`,
                        location && `"${location}"`,
                        '("official" OR "company")',
                        '("email" OR "contact")',
                        '("business hours" OR "about us")',
                        '-"personal"',
                        'bio'
                    ].filter(Boolean);
                    return parts.join(' ');
                },
                'tiktok.com': () => {
                    const parts = [
                        `site:${platform}`,
                        `"business"`,
                        `"${niche}"`,
                        location && `"${location}"`,
                        '("company" OR "official")',
                        '("email" OR "contact")',
                        '-"personal"',
                        'bio'
                    ].filter(Boolean);
                    return parts.join(' ');
                }
            };

            // Get the appropriate query pattern or use a default one
            const buildQuery = queryPatterns[platform] || (() => {
                const parts = [
                    `site:${platform}`,
                    `"${niche}"`,
                    location && `"${location}"`,
                    '"email"'
                ].filter(Boolean);
                return parts.join(' ');
            });

            return buildQuery();
        },
        async searchLeads(page = 1) {
            this.loading = true;
            this.error = null;
            try {
                if (this.filters.source === 'google') {
                    const query = this.buildSearchQuery();
                    const response = await fetch(`api/leads.php?source=google&query=${encodeURIComponent(query)}&page=${page}`);
                    const data = await response.json();
                    
                    // Check if we have items in the response
                    if (!data.items || data.items.length === 0) {
                        this.leads = [];
                        this.pagination.totalItems = 0;
                        this.loading = false;
                        return;
                    }
                    
                    // Transform Google results into lead format
                    this.leads = data.items.map(item => {
                        // Extract company name from title
                        const company_name = item.title;
                        
                        // Extract location from snippet if available
                        const locationMatch = item.snippet.match(/"([^"]*)"/) || ['', this.filters.location || ''];
                        const location = locationMatch[1];
                        
                        const lead = {
                            company_name: company_name,
                            niche: this.filters.niche,
                            location: location,
                            url: item.link,
                            platform: this.filters.platform,
                            snippet: item.snippet,
                            inDatabase: item.inDatabase,
                            status: item.inDatabase ? item.leadStatus : 'new'
                        };
                        
                        return lead;
                    });

                    this.pagination = {
                        currentPage: page,
                        totalPages: Math.ceil(data.searchInformation.totalResults / 10),
                        totalItems: data.searchInformation.totalResults,
                        perPage: 10
                    };
                } else {
                    // Database search
                    const queryParams = new URLSearchParams({
                        source: 'database',
                        niche: this.filters.niche || '',
                        location: this.filters.location || '',
                        page: page
                    }).toString();

                    const response = await fetch(`api/leads.php?${queryParams}`);
                    const data = await response.json();
                    
                    if (!data.leads || data.leads.length === 0) {
                        this.leads = [];
                        this.pagination = {
                            currentPage: 1,
                            totalPages: 0,
                            totalItems: 0,
                            perPage: 10
                        };
                        this.showAlert('No leads found matching your criteria', 'info');
                        return;
                    }

                    this.leads = data.leads.map(lead => ({
                        ...lead,
                        inDatabase: true
                    }));

                    this.pagination = data.pagination;
                    
                    const resultsText = `Found ${data.pagination.totalItems} lead${data.pagination.totalItems !== 1 ? 's' : ''}`;
                    this.showAlert(resultsText, 'success');
                }
            } catch (error) {
                console.error('Error searching leads:', error);
                this.error = error.message;
                this.showAlert('Error searching leads: ' + error.message, 'danger');
            } finally {
                this.loading = false;
            }
        },
        showAddLeadModal() {
            this.isEditing = false;
            this.resetCurrentLead();
            new bootstrap.Modal(document.getElementById('leadModal')).show();
        },
        async saveLead() {
            try {
                this.loading = true;
                const method = this.isEditing ? 'PUT' : 'POST';
                const response = await fetch('api/leads.php', {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(this.currentLead)
                });

                const result = await response.json();
                
                if (result.success) {
                    this.showAlert(this.isEditing ? 'Lead successfully updated!' : 'Lead successfully added!');
                    await this.searchLeads();
                    this.closeModal();
                } else {
                    throw new Error(result.message || 'Failed to save lead');
                }
            } catch (error) {
                this.showAlert(error.message, 'danger');
            } finally {
                this.loading = false;
            }
        },
        async deleteLead(id) {
            if (!confirm('Are you sure you want to delete this lead?')) return;
            
            try {
                this.loading = true;
                const response = await fetch(`api/leads.php?id=${id}`, {
                    method: 'DELETE'
                });

                const result = await response.json();
                
                if (result.success) {
                    this.showAlert('Lead successfully deleted!', 'warning');
                    await this.searchLeads();
                } else {
                    throw new Error(result.message || 'Failed to delete lead');
                }
            } catch (error) {
                this.showAlert(error.message, 'danger');
            } finally {
                this.loading = false;
            }
        },
        editLead(lead) {
            this.isEditing = true;
            this.currentLead = {
                id: lead.id,
                company_name: lead.company_name,
                niche: lead.niche,
                location: lead.location,
                contact_person: lead.contact_person || '',
                email: lead.email || '',
                phone: lead.phone || '',
                notes: lead.notes || '',
                platform: lead.platform || '',
                url: lead.url || '',
                status: lead.status || 'new'
            };
            new bootstrap.Modal(document.getElementById('leadModal')).show();
        },
        viewLead(lead) {
            this.currentLead = { ...lead };
            new bootstrap.Modal(document.getElementById('leadModal')).show();
        },
        async exportLeads() {
            try {
                const response = await axios.get('api/export.php', {
                    responseType: 'blob'
                });
                
                const url = window.URL.createObjectURL(new Blob([response.data]));
                const link = document.createElement('a');
                link.href = url;
                link.setAttribute('download', 'leads.csv');
                document.body.appendChild(link);
                link.click();
                link.remove();
            } catch (error) {
                console.error('Error exporting leads:', error);
                alert('Error exporting leads. Please try again.');
            }
        },
        async addToDatabase(lead) {
            try {
                // Ensure required fields are present
                if (!lead.company_name?.trim()) {
                    throw new Error('Company name is required');
                }
                if (!this.filters.niche?.trim() && !lead.niche?.trim()) {
                    throw new Error('Niche is required');
                }
                if (!this.filters.location?.trim() && !lead.location?.trim()) {
                    throw new Error('Location is required');
                }

                this.loading = true;
                const response = await fetch('api/leads.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        company_name: lead.company_name.trim(),
                        niche: (lead.niche || this.filters.niche).trim(),
                        location: (lead.location || this.filters.location).trim(),
                        contact_person: lead.contact_person || '',
                        email: lead.email || '',
                        phone: lead.phone || '',
                        notes: lead.notes || '',
                        platform: lead.platform || this.filters.platform,
                        url: lead.url || ''
                    })
                });

                const result = await response.json();
                
                if (!response.ok) {
                    throw new Error(result.message || 'Failed to add lead');
                }
                
                if (result.success) {
                    lead.inDatabase = true;
                    lead.id = result.leadId;
                    lead.status = result.status;
                    this.showAlert(result.message, 'success');
                    if (this.filters.source === 'database') {
                        await this.searchLeads(); // Refresh only if in database view
                    }
                } else {
                    throw new Error(result.message || 'Failed to add lead');
                }
            } catch (error) {
                console.error('Error adding lead:', error);
                this.showAlert(error.message, 'danger');
            } finally {
                this.loading = false;
            }
        },
        toggleSource() {
            // Reset filters when switching sources
            this.filters = {
                ...this.filters,
                niche: '',
                location: ''
            };
            this.leads = [];
            this.pagination = {
                currentPage: 1,
                totalPages: 0,
                totalItems: 0,
                perPage: 10
            };
            
            if (this.filters.source === 'database') {
                // Automatically load all leads when switching to database
                this.searchLeads(1);
            }
        },
        changePage(page) {
            if (page >= 1 && page <= this.pagination.totalPages) {
                this.searchLeads(page);
            }
        },
        showAlert(message, type = 'success') {
            // Clear any existing timeout
            if (this.alert.timeout) {
                clearTimeout(this.alert.timeout);
            }
            
            // Show new alert
            this.alert.show = true;
            this.alert.message = message;
            this.alert.type = type;
            
            // Auto hide after 5 seconds
            this.alert.timeout = setTimeout(() => {
                this.alert.show = false;
            }, 5000);
        },
        resetCurrentLead() {
            this.currentLead = {
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
            };
        }
    },
    computed: {
        pages() {
            const pages = [];
            let start = Math.max(1, this.pagination.currentPage - 2);
            let end = Math.min(this.pagination.totalPages, start + 4);
            
            if (end - start < 4) {
                start = Math.max(1, end - 4);
            }
            
            for (let i = start; i <= end; i++) {
                pages.push(i);
            }
            
            return pages;
        }
    },
    mounted() {
        // Initialize with Google search view
        this.toggleSource();
        
        // Load any existing leads
        this.searchLeads();
        
        // Add event listener for Enter key
        document.addEventListener('keyup', (e) => {
            if (e.key === 'Enter' && document.activeElement.id === 'searchQuery') {
                this.searchLeads();
            }
        });
    }
}).mount('#app')
