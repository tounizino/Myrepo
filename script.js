/**
 * Cloud Loadout - Hero Section Interactions
 * Lightweight, performance-optimized JS
 */

(function() {
  'use strict';

  // DOM Ready
  document.addEventListener('DOMContentLoaded', init);

  function init() {
    initMobileNav();
    initMetricBars();
    initScrollEffects();
    initIntersectionObserver();
  }

  // Mobile Navigation Toggle
  function initMobileNav() {
    const toggle = document.querySelector('.mobile-toggle');
    const navLinks = document.querySelector('.nav-links');
    
    if (!toggle || !navLinks) return;

    toggle.addEventListener('click', function() {
      const isExpanded = this.getAttribute('aria-expanded') === 'true';
      this.setAttribute('aria-expanded', !isExpanded);
      navLinks.classList.toggle('active');
      this.classList.toggle('active');
    });

    // Close on link click
    navLinks.querySelectorAll('a').forEach(link => {
      link.addEventListener('click', () => {
        toggle.setAttribute('aria-expanded', 'false');
        navLinks.classList.remove('active');
        toggle.classList.remove('active');
      });
    });

    // Close on outside click
    document.addEventListener('click', (e) => {
      if (!e.target.closest('.nav')) {
        toggle.setAttribute('aria-expanded', 'false');
        navLinks.classList.remove('active');
        toggle.classList.remove('active');
      }
    });
  }

  // Metric Bar Animations
  function initMetricBars() {
    const metricFills = document.querySelectorAll('.metric-fill');
    
    metricFills.forEach(fill => {
      const value = fill.dataset.value;
      if (value) {
        fill.style.width = `${value}%`;
      }
    });
  }

  // Scroll Effects
  function initScrollEffects() {
    const header = document.querySelector('.header');
    if (!header) return;

    let lastScroll = 0;
    const scrollThreshold = 50;

    window.addEventListener('scroll', throttle(handleScroll, 16), { passive: true });

    function handleScroll() {
      const currentScroll = window.scrollY;
      
      if (currentScroll > scrollThreshold) {
        header.classList.add('scrolled');
      } else {
        header.classList.remove('scrolled');
      }
      
      lastScroll = currentScroll;
    }

    function throttle(func, limit) {
      let inThrottle;
      return function(...args) {
        if (!inThrottle) {
          func.apply(this, args);
          inThrottle = true;
          setTimeout(() => inThrottle = false, limit);
        }
      };
    }
  }

  // Intersection Observer for Animations
  function initIntersectionObserver() {
    if (!('IntersectionObserver' in window)) {
      document.querySelectorAll('.feature-card').forEach(card => {
        card.style.opacity = '1';
        card.style.transform = 'none';
      });
      return;
    }

    const observerOptions = {
      root: null,
      rootMargin: '0px 0px -50px 0px',
      threshold: 0.1
    };

    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('in-view');
          observer.unobserve(entry.target);
        }
      });
    }, observerOptions);

    document.querySelectorAll('.feature-card').forEach((card, index) => {
      card.style.opacity = '0';
      card.style.transform = 'translateY(20px)';
      card.style.transition = `opacity 0.5s ease ${index * 0.1}s, transform 0.5s ease ${index * 0.1}s`;
      observer.observe(card);
    });
  }

  // Smooth scroll for anchor links
  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
      const targetId = this.getAttribute('href');
      if (targetId === '#') return;
      
      const target = document.querySelector(targetId);
      if (target) {
        e.preventDefault();
        const headerOffset = 80;
        const elementPosition = target.getBoundingClientRect().top;
        const offsetPosition = elementPosition + window.scrollY - headerOffset;

        window.scrollTo({
          top: offsetPosition,
          behavior: 'smooth'
        });
      }
    });
  });

})();
