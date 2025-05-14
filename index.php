<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lida - Lead Management System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
            <div class="container">
                <a class="navbar-brand" href="index.php"><img src="assets/images/lidanew.png" alt="Lida Logo" height="60px"></a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link active text-dark" href="index.php">Search</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-dark" href="myleads.php">My Leads</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Alert Component -->
        <div v-if="alert.show" 
                 class="alert"
                 :class="'alert-' + alert.type"
                 role="alert"
                 style="position: fixed; top: 20px; right: 20px; z-index: 1050; min-width: 300px; box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);">
                <div class="d-flex align-items-center">
                    <i class="fas me-2"
                       :class="{
                           'fa-check-circle': alert.type === 'success',
                           'fa-exclamation-circle': alert.type === 'danger',
                           'fa-exclamation-triangle': alert.type === 'warning'
                       }">
                    </i>
                    {{ alert.message }}
                    <button type="button" 
                            class="btn-close ms-auto" 
                            @click="alert.show = false"
                            aria-label="Close">
                    </button>
                </div>
            </div>


        <div class="container mt-4">
            <div class="row">
                <div class="col-md-12 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-2">
                                    <select v-model="filters.source" @change="toggleSource" class="form-select">
                                        <option value="google">Google Search</option>
                                        <option value="database">Database Search</option>
                                    </select>
                                </div>
                                <div v-if="filters.source === 'google'" class="col-md-2">
                                    <select v-model="filters.platform" class="form-select">
                                        <option v-for="platform in platforms" :key="platform.value" :value="platform.value">
                                            {{ platform.label }}
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select v-model="filters.niche" class="form-select">
                                        <option value="">Select Niche</option>
                                        <optgroup v-for="category in niches" 
                                                :key="category.category" 
                                                :label="category.category">
                                            <option v-for="item in category.items" 
                                                    :key="item" 
                                                    :value="item">
                                                {{ item }}
                                            </option>
                                        </optgroup>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select v-model="filters.location" 
                                           class="form-select" 
                                           :placeholder="filters.source === 'google' ? 'Select location' : 'Search by location...'"
                                           @change="searchLeads(1)">
                                           <option value="">Select Location</option>
<option value="Nairobi">Nairobi</option>
<option value="Mombasa">Mombasa</option>
<option value="Kisumu">Kisumu</option>
<option value="Nakuru">Nakuru</option>
<option value="Eldoret">Eldoret</option>
<option value="Thika">Thika</option>
<option value="Machakos">Machakos</option>
<option value="Naivasha">Naivasha</option>
<option value="Meru">Meru</option>
<option value="Kisii">Kisii</option>
<option value="Kilifi">Kilifi</option>
<option value="Garissa">Garissa</option>
<option value="Kakamega">Kakamega</option>
<option value="Kericho">Kericho</option>
<option value="Ruiru">Ruiru</option>
<option value="Limuru">Limuru</option>

                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <button @click="searchLeads(1)" class="btn btn-primary" :disabled="loading">
                                        <span v-if="loading" class="spinner-border spinner-border-sm" role="status"></span>
                                        {{ loading ? 'Searching...' : 'Search' }}
                                    </button>
                                    <button v-if="filters.source === 'database'" @click="showAddLeadModal" class="btn btn-success ms-2">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>
<!-- <div v-if="filters.source === 'google'" class="row mt-3">
                                <div class="col-12">
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i> 
                                        Search Format: 
                                        <code v-if="filters.platform === 'linkedin.com'">
                                            site:linkedin.com/company "{{ filters.niche || 'niche' }}" "{{ filters.location || 'location' }}" "email"
                                        </code>
                                        <code v-else-if="filters.platform === 'x.com'">
                                            site:x.com bio "{{ filters.niche || 'niche' }}" "{{ filters.location || 'location' }}" "email" OR "gmail"
                                        </code>
                                        <code v-else-if="filters.platform === 'facebook.com'">
                                            site:facebook.com/pages "{{ filters.niche || 'niche' }}" "{{ filters.location || 'location' }}" "email" OR "contact"
                                        </code>
                                        <code v-else-if="filters.platform === 'instagram.com'">
                                            site:instagram.com "{{ filters.niche || 'niche' }}" "{{ filters.location || 'location' }}" "email" OR "gmail" OR "contact" bio
                                        </code>
                                        <code v-else-if="filters.platform === 'tiktok.com'">
                                            site:tiktok.com "{{ filters.niche || 'niche' }}" "{{ filters.location || 'location' }}" "email" OR "business" OR "contact" bio
                                        </code>
                                    </div>
                                </div>
                            </div>-->
                        </div>
                    </div>
                </div>

                <!-- Leads Table -->
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table align-middle">
                                    <thead>
                                        <tr>
                                            <th style="width: 80px">Site</th>
                                            <th>Company</th>
                                            <th>Niche</th>
                                            <th>Location</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="lead in leads" :key="lead.id || lead.url">
                                            <td>
                                                <div class="placeholder-image" :class="'platform-' + getPlatformClass(lead.platform || filters.platform)">
                                                    <i class="fab" :class="getPlatformIcon(lead.platform || filters.platform)"></i>
                                                </div>
                                            </td>
                                            <td>{{ lead.company_name }}</td>
                                            <td>{{ lead.niche }}</td>
                                            <td>{{ lead.location }}</td>
                                            <td>
                                                <span :class="'badge bg-' + getStatusColor(lead.status)">
                                                    {{ lead.status }}
                                                </span>
                                            </td>
                                            <td>
                                                <button @click="viewLead(lead)" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button v-if="filters.source === 'database'" @click="editLead(lead)" class="btn btn-sm btn-warning ms-1">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button v-if="filters.source === 'database'" @click="deleteLead(lead.id)" class="btn btn-sm btn-danger ms-1">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                                <a v-if="lead.url" :href="lead.url" target="_blank" class="btn btn-sm btn-primary ms-1">
                                                    <i class="fas fa-external-link-alt"></i>
                                                </a>
                                                <button 
                                                    v-if="filters.source === 'google' && !lead.inDatabase"
                                                    @click="addToDatabase(lead)" 
                                                    class="btn btn-sm btn-success ms-1">
                                                    <i class="fas fa-plus"></i> Add Lead
                                                </button>
                                                <span 
                                                    v-if="filters.source === 'google' && lead.inDatabase"
                                                    class="badge bg-success ms-1">
                                                    In Database
                                                </span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div v-if="pagination.totalPages > 1" class="d-flex justify-content-center mt-4">
                                <nav aria-label="Page navigation">
                                    <ul class="pagination">
                                        <li class="page-item" :class="{ disabled: pagination.currentPage === 1 }">
                                            <a class="page-link" href="#" @click.prevent="changePage(pagination.currentPage - 1)">
                                                <i class="fas fa-chevron-left"></i>
                                            </a>
                                        </li>
                                        <li v-for="page in pages" 
                                            :key="page" 
                                            class="page-item"
                                            :class="{ active: page === pagination.currentPage }">
                                            <a class="page-link" href="#" @click.prevent="changePage(page)">{{ page }}</a>
                                        </li>
                                        <li class="page-item" :class="{ disabled: pagination.currentPage === pagination.totalPages }">
                                            <a class="page-link" href="#" @click.prevent="changePage(pagination.currentPage + 1)">
                                                <i class="fas fa-chevron-right"></i>
                                            </a>
                                        </li>
                                    </ul>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add/Edit Lead Modal -->
        <div class="modal fade" id="leadModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ isEditing ? 'Edit Lead' : 'Add New Lead' }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form @submit.prevent="saveLead">
                            <div class="mb-3">
                                <label class="form-label">Company Name</label>
                                <input type="text" v-model="currentLead.company_name" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Niche</label>
                                <input type="text" v-model="currentLead.niche" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Location</label>
                                <input type="text" v-model="currentLead.location" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select v-model="currentLead.status" class="form-control">
                                    <option value="new">New</option>
                                    <option value="contacted">Contacted</option>
                                    <option value="follow_up">Follow Up</option>
                                    <option value="converted">Converted</option>
                                    <option value="not_interested">Not Interested</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Platform</label>
                                <select v-model="currentLead.platform" class="form-control">
                                    <option value="">Select Platform</option>
                                    <option v-for="platform in platforms" :key="platform.value" :value="platform.value">
                                        {{ platform.label }}
                                    </option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Website/Profile URL</label>
                                <input type="url" v-model="currentLead.url" class="form-control" placeholder="https://">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Contact Person</label>
                                <input type="text" v-model="currentLead.contact_person" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" v-model="currentLead.email" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Phone</label>
                                <input type="tel" v-model="currentLead.phone" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Notes</label>
                                <textarea v-model="currentLead.notes" class="form-control" rows="3"></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Save</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/app.js"></script>
</body>
</html>
