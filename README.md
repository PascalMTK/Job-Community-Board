# JobCommunity Board

JobCommunity Board is a practical web-based platform created to make it easier for students to discover local job opportunities and for employers to find new talent. It was developed as part of the WAD621S Web Application Development course and focuses on solving a real challenge faced by students entering the job market.

## Table of Contents

1. Project Overview
2. Team Information
3. Background and Problem Statement
4. Solution Overview
5. Core Features
6. Technical Architecture
7. Project Structure
8. Installation & Setup
9. Development Approach
10. Target Users & Benefits
11. Project Outcomes
12. Security Framework
13. Future Roadmap

## 1. Project Overview

JobCommunity Board serves as a specialized job-matching platform focused on students and local businesses. Its goal is to simplify the hiring and job-seeking process while supporting regional talent retention.

## 2. Team Information

**Group 83 Members**

* Awike Gulu (224008595)
* Nandesora Kavari (223013870)
* Pascal Mutaka (224084038)

**Academic Context**

* Course: WAD621S Web Application Development
* Lecturer: Mr. Wilfried Kongolo
* Submission Date: September 2025

## 3. Background and Problem Statement

Students entering the job market often struggle to find opportunities that align with their skills or academic commitments. Traditional job portals are not tailored to their needs, and many local employers lack affordable and effective platforms to discover new talent.

## 4. Solution Overview

JobCommunity Board directly addresses these challenges by providing a targeted recruitment ecosystem focused on student employment. The platform enables students to build professional profiles, search for relevant opportunities, and apply directly, while employers can easily post jobs and manage candidates.

## 5. Core Features

### User Experience and Design Choices

The platform uses blue as its primary color because it conveys trust, reliability, and professionalism — values that are important in a job-seeking environment. A built-in dark mode is also available to improve accessibility and reduce eye strain during extended browsing. In addition, users can switch the interface language between English and Afrikaans to ensure inclusivity and accommodate different communities in the region.

### For Students and Job Seekers

* Job browsing with detailed listings
* Personal dashboards for managing profiles and applications
* Direct job applications
* Professional profile management
* Advanced search and filtering

### For Employers

* Job posting tools
* Employer dashboards
* Candidate application tracking
* Customizable company profiles

### Administrative Capabilities

* User account management
* Job post moderation
* System performance analytics

## 6. Technical Architecture

### Technology Stack

**Frontend**

* HTML5
* CSS3
* JavaScript

**Backend**

* PHP
* MySQL

**Deployment & Infrastructure**

* Docker and Docker Compose
* Apache HTTP Server

**Security Layer**

* Secure session management
* SQL injection prevention
* Multi-layered input validation

## 7. Project Structure

```
Job-Community-Board/
├── database/
│   └── schema.sql
├── includes/
│   ├── connection.php
│   ├── session.php
│   ├── functions.php
│   ├── header.php
│   └── footer.php
├── html/
│   ├── home.html
│   ├── login.html
│   ├── register.html
│   ├── jobs.html
│   ├── dashboard.html
│   └── [additional templates]
├── css/
│   ├── style.css
│   └── forms.css
├── js/
├── assets/
├── docker-compose.yml
├── Dockerfile
└── [application PHP files]
```

## 8. Installation & Setup

### Prerequisites

* Docker installed
* Docker Compose installed

### Quick Start

Clone the repository:

```
git clone <repository-url>
cd Job-Community-Board
```

Deploy the application:

```
docker-compose up -d
```

Initialize the database:

```
docker exec -i <mysql-container> mysql -u<user> -p<password> < database/schema.sql
```

Access the platform:

```
http://localhost:8080
```

## 9. Development Approach

An Iterative Development Methodology was used, enabling:

* Progressive delivery of core features
* Early user feedback integration
* UI and UX enhancements across iterations
* Phased security implementation

## 10. Target Users & Benefits

The platform serves three main user groups:

* Students and graduates who are looking for entry-level roles and internships.
* Local employers who want a simple and cost-effective way to recruit emerging talent.
* Educational institutions that aim to support students in transitioning into the job market.
  Enhanced support for graduate employability       |

## 11. Project Outcomes

* Fully deployed and functional platform
* Secure authentication and session handling
* Robust MySQL-backed architecture
* Responsive and intuitive user interface
* Facilitates student workforce participation

## 12. Security Framework

* Strong authentication procedures
* SQL injection prevention safeguards
* Secure form handling and sanitation
* Protected file upload processes
* Session hijacking prevention measures

## 13. Future Roadmap

* Improved search filters and recommendation systems
* Automated email or SMS job alerts
* Built-in resume and portfolio builder
* Native mobile application
* Employer analytics dashboard
* Social media integrations
