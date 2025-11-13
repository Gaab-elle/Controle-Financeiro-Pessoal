<template>
  <div class="min-h-screen" style="background-color: #F4F6FA;">
    <!-- Navbar Fixo no Topo -->
    <nav class="navbar navbar-fixed-top">
      <div class="navbar-inner header">
        <div class="container">
          <!-- Logo/Brand à esquerda -->
          <div class="brand">
            <img 
              src="/images/logo2.png" 
              alt="TôNoAzul Logo" 
              class="brand-logo"
              @error="showFallback = true"
              v-show="!showFallback"
            />
            <i v-show="showFallback" class="fas fa-heart brand-fallback" style="color: #2196F3;"></i>
          </div>
          
          <!-- Links de navegação -->
          <ul class="nav nav-links">
            <li>
              <router-link 
                to="/" 
                class="nav-link"
                :class="{ active: $route.path === '/' }"
              >
                INÍCIO
              </router-link>
            </li>
            <li>
              <router-link 
                to="/contas" 
                class="nav-link"
                :class="{ active: $route.path === '/contas' }"
              >
                CONTAS
              </router-link>
            </li>
            <li>
              <router-link 
                to="/lancamentos" 
                class="nav-link"
                :class="{ active: $route.path === '/lancamentos' }"
              >
                LANÇAMENTOS
              </router-link>
            </li>
          </ul>
          
          <!-- Usuário e botão Sair à direita -->
          <div class="nav-right">
            <span class="user-name">{{ authStore.user?.name }}</span>
            <button
              @click="handleLogout"
              class="nav-link logout-btn"
              type="button"
            >
              SAIR
            </button>
          </div>
        </div>
      </div>
    </nav>

    <!-- Conteúdo principal com margem para o navbar fixo -->
    <main class="main-content">
      <router-view />
    </main>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const router = useRouter()
const authStore = useAuthStore()

const showFallback = ref(false)

async function handleLogout() {
  await authStore.logout()
  router.push('/login')
}
</script>

<style scoped>
/* Navbar fixo no topo */
.navbar {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  z-index: 1000;
  margin: 0;
  border: 0;
}

.navbar-inner {
  padding: 0;
}

.header {
  background-color: rgba(255, 255, 255, 0.95) !important;
  background-image: none !important;
  border-bottom: 2px solid #2196F3 !important;
  box-shadow: 0 12px 6px -6px rgba(0, 0, 0, 0.2) !important;
}

.container {
  max-width: 1400px;
  margin: 0 auto;
  padding: 0 30px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  min-height: 60px;
  height: 60px;
  width: 100%;
}

/* Brand/Logo */
.brand {
  display: flex;
  align-items: center;
  letter-spacing: 4px;
  border-top: 4px solid white;
  transition: border-top 0.3s;
  padding: 0;
  cursor: pointer;
  height: 60px;
  overflow: visible;
}

.brand:hover {
  border-top: 4px solid #2196F3;
}

.brand-logo {
  height: 100px;
  width: 100px;
  min-width: 75px;
  min-height: 75px;
  object-fit: contain;
  margin: -8px 0;
}

.brand-fallback {
  font-size: 40px;
  line-height: 1;
  margin: -8px 0;
  display: inline-block;
}

/* Links de navegação */
.nav {
  display: flex;
  list-style: none;
  margin: 0;
  padding: 0;
  align-items: center;
}

.nav-links {
  gap: 0;
  flex: 1;
  justify-content: center;
  margin: 0;
  padding: 0;
}

.nav-links li {
  line-height: 60px;
  letter-spacing: 2px;
}

.nav-link {
  display: block;
  padding: 8px 18px;
  text-decoration: none;
  color: #0A192F;
  font-weight: 600;
  font-size: 11px;
  text-transform: uppercase;
  border-top: 4px solid white;
  transition: border-top 0.3s, color 0.3s;
  cursor: pointer;
  background: none;
  border-left: none;
  border-right: none;
  border-bottom: none;
  letter-spacing: 2px;
}

.nav-link:hover {
  border-top: 4px solid #2196F3;
  color: #2196F3;
}

.nav-link.active {
  border-top: 4px solid #2196F3;
  color: #2196F3;
}

/* Navegação à direita (usuário e sair) */
.nav-right {
  display: flex;
  align-items: center;
  gap: 15px;
  margin-left: auto;
}

.user-name {
  color: #0A192F;
  font-weight: 500;
  font-size: 13px;
  letter-spacing: 1px;
  padding-top: 0;
}

.logout-btn {
  border: none;
  background: none;
  padding: 8px 18px;
  border-top: 4px solid white;
  transition: border-top 0.3s, color 0.3s;
  cursor: pointer;
  font-weight: 600;
  font-size: 11px;
  text-transform: uppercase;
  letter-spacing: 2px;
  color: #0A192F;
}

.logout-btn:hover {
  border-top: 4px solid #2196F3;
  color: #2196F3;
}

/* Conteúdo principal */
.main-content {
  margin-top: 60px;
  padding: 20px;
  max-width: 1200px;
  margin-left: auto;
  margin-right: auto;
}

/* Responsividade */
@media (max-width: 768px) {
  .container {
    flex-wrap: wrap;
    height: auto;
    padding: 10px;
  }
  
  .nav-links {
    width: 100%;
    order: 3;
    justify-content: space-around;
    margin-top: 10px;
  }
  
  .nav-link {
    padding: 5px 10px;
    font-size: 11px;
  }
  
  .nav-right {
    order: 2;
    margin-left: 0;
    gap: 10px;
  }
  
  .user-name {
    font-size: 12px;
    padding-top: 6px;
  }
  
  .logout-btn {
    padding: 5px 10px;
    font-size: 11px;
  }
  
  .main-content {
    margin-top: 80px;
  }
  
  .brand-logo {
    height: 60px;
    width: 60px;
    min-width: 60px;
    min-height: 60px;
    margin: -5px 0;
  }
  
  .brand {
    height: 50px;
  }
  
  .container {
    min-height: 50px;
    height: 50px;
  }
  
  .nav-links li {
    line-height: 50px;
  }
}
</style>

