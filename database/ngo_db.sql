-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 24, 2025 at 09:09 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ngo_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `badges`
--

CREATE TABLE `badges` (
  `id` int(11) NOT NULL,
  `volunteer_id` int(11) DEFAULT NULL,
  `badge_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `awarded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `certificates`
--

CREATE TABLE `certificates` (
  `id` int(11) NOT NULL,
  `volunteer_id` int(11) NOT NULL,
  `layout_id` int(11) NOT NULL,
  `issued_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `certificate_layouts`
--

CREATE TABLE `certificate_layouts` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `background_color` varchar(7) DEFAULT '#FFFFFF',
  `logo` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `certificate_layouts`
--

INSERT INTO `certificate_layouts` (`id`, `title`, `description`, `background_color`, `logo`, `created_at`) VALUES
(9, 'xyz ', 'z', '#FFFFFF', 'logo_1751355452_WhatsApp Image 2025-06-29 at 8.53.19 AM.jpg', '2025-07-01 07:37:32');

-- --------------------------------------------------------

--
-- Table structure for table `contact_submissions`
--

CREATE TABLE `contact_submissions` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `contact_submissions`
--

INSERT INTO `contact_submissions` (`id`, `name`, `email`, `message`, `submitted_at`) VALUES
(1, 'Jane Smith', 'jane@example.com', 'Interested in volunteering opportunities.', '2025-07-01 07:30:34');

-- --------------------------------------------------------

--
-- Table structure for table `impact_portfolio`
--

CREATE TABLE `impact_portfolio` (
  `id` int(11) NOT NULL,
  `volunteer_id` int(11) NOT NULL,
  `hours` int(11) DEFAULT 0,
  `tasks_completed` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `learning_resources`
--

CREATE TABLE `learning_resources` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `category` varchar(100) NOT NULL,
  `url` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `duration` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `learning_resources`
--

INSERT INTO `learning_resources` (`id`, `title`, `category`, `url`, `description`, `duration`, `created_at`) VALUES
(1, 'Introduction to Child Education', 'Child Education', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'Overview of early childhood education principles.', '10:00', '2025-07-01 08:56:01'),
(2, 'Effective Teaching Techniques', 'Child Education', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'Strategies for engaging young learners.', '12:30', '2025-07-01 08:56:01'),
(3, 'Child Psychology Basics', 'Child Education', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'Understanding child behavior and development.', '15:45', '2025-07-01 08:56:01'),
(4, 'Creating Safe Learning Spaces', 'Child Education', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'How to foster a supportive classroom environment.', '8:20', '2025-07-01 08:56:01'),
(5, 'Literacy Programs for Kids', 'Child Education', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'Implementing reading initiatives for children.', '11:10', '2025-07-01 08:56:01'),
(6, 'Math Tutoring for Young Learners', 'Child Education', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'Fun ways to teach math to kids.', '9:50', '2025-07-01 08:56:01'),
(7, 'Storytelling for Engagement', 'Child Education', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'Using stories to connect with children.', '14:00', '2025-07-01 08:56:01'),
(8, 'Art in Education', 'Child Education', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'Incorporating art into learning activities.', '13:25', '2025-07-01 08:56:01'),
(9, 'Music and Movement for Kids', 'Child Education', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'Benefits of music in child development.', '10:15', '2025-07-01 08:56:01'),
(10, 'STEM Activities for Children', 'Child Education', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'Hands-on STEM projects for kids.', '12:00', '2025-07-01 08:56:01'),
(11, 'Parental Involvement in Education', 'Child Education', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'Engaging parents in learning.', '11:30', '2025-07-01 08:56:01'),
(12, 'Inclusive Education Practices', 'Child Education', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'Supporting diverse learners.', '10:40', '2025-07-01 08:56:01'),
(13, 'Early Language Development', 'Child Education', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'Promoting language skills in kids.', '9:15', '2025-07-01 08:56:01'),
(14, 'Social Skills in Classrooms', 'Child Education', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'Teaching cooperation and empathy.', '8:45', '2025-07-01 08:56:01'),
(15, 'Play-Based Learning', 'Child Education', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'Using play to enhance learning.', '12:20', '2025-07-01 08:56:01'),
(16, 'Volunteer Orientation', 'Volunteer Skills', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'Introduction to volunteering with NGOs.', '10:00', '2025-07-01 08:56:01'),
(17, 'Effective Communication', 'Volunteer Skills', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'Building strong communication skills.', '11:00', '2025-07-01 08:56:01'),
(18, 'Teamwork in NGOs', 'Volunteer Skills', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'Collaborating with other volunteers.', '9:30', '2025-07-01 08:56:01'),
(19, 'Time Management for Volunteers', 'Volunteer Skills', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'Balancing volunteer commitments.', '8:00', '2025-07-01 08:56:01'),
(20, 'Fundraising Basics', 'Volunteer Skills', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'How to raise funds for NGOs.', '12:15', '2025-07-01 08:56:01'),
(21, 'Event Planning for NGOs', 'Volunteer Skills', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'Organizing successful events.', '13:00', '2025-07-01 08:56:01'),
(22, 'Conflict Resolution', 'Volunteer Skills', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'Handling disputes in volunteer settings.', '10:30', '2025-07-01 08:56:01'),
(23, 'Leadership in Volunteering', 'Volunteer Skills', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'Leading volunteer teams effectively.', '11:45', '2025-07-01 08:56:01'),
(24, 'Cultural Sensitivity', 'Volunteer Skills', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'Working in diverse communities.', '9:20', '2025-07-01 08:56:01'),
(25, 'Volunteer Safety Protocols', 'Volunteer Skills', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'Ensuring safety during volunteering.', '8:50', '2025-07-01 08:56:01'),
(26, 'Data Entry for NGOs', 'Volunteer Skills', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'Managing data effectively.', '10:10', '2025-07-01 08:56:01'),
(27, 'Public Speaking for Outreach', 'Volunteer Skills', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'Speaking to promote NGO causes.', '11:20', '2025-07-01 08:56:01'),
(28, 'Social Media for NGOs', 'Volunteer Skills', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'Using social media for outreach.', '9:40', '2025-07-01 08:56:01'),
(29, 'Grant Writing Basics', 'Volunteer Skills', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'Writing effective grant proposals.', '12:30', '2025-07-01 08:56:01'),
(30, 'Volunteer Recruitment', 'Volunteer Skills', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'Attracting new volunteers.', '10:50', '2025-07-01 08:56:01'),
(31, 'Project Management for Volunteers', 'Volunteer Skills', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'Managing volunteer projects efficiently.', '11:15', '2025-07-01 08:56:01'),
(32, 'Digital Tools for NGOs', 'Volunteer Skills', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'Using technology to support NGO work.', '10:25', '2025-07-01 08:56:01'),
(33, 'Volunteer Motivation Techniques', 'Volunteer Skills', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'Keeping volunteers engaged.', '9:00', '2025-07-01 08:56:01'),
(34, 'Feedback and Evaluation', 'Volunteer Skills', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'Providing constructive feedback.', '8:30', '2025-07-01 08:56:01'),
(35, 'Networking for Volunteers', 'Volunteer Skills', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'Building professional connections.', '10:45', '2025-07-01 08:56:01'),
(36, 'Community Outreach Strategies', 'Community Engagement', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'Engaging local communities.', '11:00', '2025-07-01 08:56:01'),
(37, 'Organizing Clean-Up Drives', 'Community Engagement', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'Planning community clean-ups.', '9:15', '2025-07-01 08:56:01'),
(38, 'Youth Engagement Programs', 'Community Engagement', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'Involving youth in NGO activities.', '10:30', '2025-07-01 08:56:01'),
(39, 'Parent Workshops', 'Community Engagement', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'Conducting workshops for parents.', '12:00', '2025-07-01 08:56:01'),
(40, 'Community Health Initiatives', 'Community Engagement', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'Promoting health in communities.', '8:45', '2025-07-01 08:56:01'),
(41, 'Building Community Partnerships', 'Community Engagement', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'Collaborating with local organizations.', '10:20', '2025-07-01 08:56:01'),
(42, 'Volunteer-Led Events', 'Community Engagement', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'Organizing community events.', '11:30', '2025-07-01 08:56:01'),
(43, 'Social Media Campaigns', 'Community Engagement', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'Creating impactful online campaigns.', '9:50', '2025-07-01 08:56:01'),
(44, 'Community Needs Assessment', 'Community Engagement', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'Identifying community needs.', '10:15', '2025-07-01 08:56:01'),
(45, 'Organizing Charity Runs', 'Community Engagement', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'Planning charity runs for awareness.', '12:10', '2025-07-01 08:56:01'),
(46, 'Engaging Schools in Communities', 'Community Engagement', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'Working with schools for outreach.', '11:00', '2025-07-01 08:56:01'),
(47, 'Cultural Events for Inclusion', 'Community Engagement', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'Promoting cultural diversity.', '9:30', '2025-07-01 08:56:01'),
(48, 'Volunteer Appreciation Events', 'Community Engagement', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'Recognizing volunteer contributions.', '8:40', '2025-07-01 08:56:01'),
(49, 'Community Storytelling', 'Community Engagement', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'Using stories to build connections.', '10:50', '2025-07-01 08:56:01'),
(50, 'Advocacy in Communities', 'Community Engagement', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'Advocating for community issues.', '11:20', '2025-07-01 08:56:01'),
(51, 'Food Distribution Programs', 'Community Engagement', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'Organizing food drives.', '9:10', '2025-07-01 08:56:01'),
(52, 'Community Safety Workshops', 'Community Engagement', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'Promoting safety awareness.', '10:00', '2025-07-01 08:56:01'),
(53, 'Youth Mentorship Programs', 'Community Engagement', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'Mentoring young community members.', '11:45', '2025-07-01 08:56:01'),
(54, 'Public Awareness Campaigns', 'Community Engagement', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'Raising awareness for causes.', '9:25', '2025-07-01 08:56:01'),
(55, 'Community Volunteer Training', 'Community Engagement', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'Training community volunteers.', '10:35', '2025-07-01 08:56:01'),
(56, 'Child Safety Protocols', 'Child Welfare', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'Ensuring safety for children.', '9:30', '2025-07-01 08:56:01'),
(57, 'Child Nutrition Basics', 'Child Welfare', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'Promoting healthy diets for kids.', '10:00', '2025-07-01 08:56:01'),
(58, 'Mental Health Support for Kids', 'Child Welfare', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'Addressing mental health needs.', '11:15', '2025-07-01 08:56:01'),
(59, 'Child Protection Policies', 'Child Welfare', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'Understanding child protection laws.', '12:00', '2025-07-01 08:56:01'),
(60, 'Trauma-Informed Care', 'Child Welfare', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'Supporting children with trauma.', '10:45', '2025-07-01 08:56:01'),
(61, 'Preventing Child Abuse', 'Child Welfare', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'Strategies to prevent abuse.', '9:20', '2025-07-01 08:56:01'),
(62, 'Supporting Foster Children', 'Child Welfare', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'Helping children in foster care.', '11:30', '2025-07-01 08:56:01'),
(63, 'Child Health Screenings', 'Child Welfare', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'Conducting health checks for kids.', '8:50', '2025-07-01 08:56:01'),
(64, 'Emotional Well-Being', 'Child Welfare', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'Promoting emotional health in children.', '10:10', '2025-07-01 08:56:01'),
(65, 'Safe Play Environments', 'Child Welfare', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'Creating safe play spaces.', '9:40', '2025-07-01 08:56:01'),
(66, 'Child Rights Advocacy', 'Child Welfare', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'Advocating for children’s rights.', '11:00', '2025-07-01 08:56:01'),
(67, 'Nutrition Education for Families', 'Child Welfare', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'Teaching families about nutrition.', '10:20', '2025-07-01 08:56:01'),
(68, 'Supporting Children with Disabilities', 'Child Welfare', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'Inclusive support strategies.', '12:15', '2025-07-01 08:56:01'),
(69, 'Anti-Bullying Programs', 'Child Welfare', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'Preventing bullying in schools.', '9:00', '2025-07-01 08:56:01'),
(70, 'Child Mental Health First Aid', 'Child Welfare', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'Basic mental health support skills.', '10:30', '2025-07-01 08:56:01'),
(71, 'Hygiene Education for Kids', 'Child Welfare', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'Teaching hygiene practices.', '8:45', '2025-07-01 08:56:01'),
(72, 'Child Safety in Emergencies', 'Child Welfare', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'Preparing for emergencies.', '11:10', '2025-07-01 08:56:01'),
(73, 'Counseling for Children', 'Child Welfare', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'Basic counseling techniques.', '10:00', '2025-07-01 08:56:01'),
(74, 'Child Welfare Case Management', 'Child Welfare', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'Managing child welfare cases.', '12:30', '2025-07-01 08:56:01'),
(75, 'Family Support Strategies', 'Child Welfare', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'Supporting families in need.', '9:50', '2025-07-01 08:56:01'),
(76, 'Recognizing Child Abuse Signs', 'Child Welfare', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'Identifying abuse indicators.', '10:15', '2025-07-01 08:56:01'),
(77, 'Child Welfare Policy Overview', 'Child Welfare', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'Understanding welfare policies.', '11:40', '2025-07-01 08:56:01'),
(78, 'Supporting At-Risk Youth', 'Child Welfare', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'Helping vulnerable youth.', '9:30', '2025-07-01 08:56:01'),
(79, 'Child Safety Training', 'Child Welfare', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'Training for child safety.', '10:50', '2025-07-01 08:56:01'),
(80, 'Parental Support Programs', 'Child Welfare', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'Programs to support parents.', '11:20', '2025-07-01 08:56:01'),
(81, 'NGO Fundraising Basics', 'NGO Operations', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'How to raise funds for NGOs.', '12:15', '2025-07-01 08:56:01'),
(82, 'Grant Writing for NGOs', 'NGO Operations', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'Crafting successful grant proposals.', '11:30', '2025-07-01 08:56:01'),
(83, 'NGO Budget Management', 'NGO Operations', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'Managing NGO finances.', '10:00', '2025-07-01 08:56:01'),
(84, 'Program Evaluation', 'NGO Operations', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'Evaluating NGO programs.', '9:45', '2025-07-01 08:56:01'),
(85, 'Volunteer Coordination', 'NGO Operations', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'Managing volunteer teams.', '11:00', '2025-07-01 08:56:01'),
(86, 'NGO Compliance Basics', 'NGO Operations', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'Understanding legal compliance.', '10:30', '2025-07-01 08:56:01'),
(87, 'Strategic Planning for NGOs', 'NGO Operations', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'Creating strategic plans.', '12:00', '2025-07-01 08:56:01'),
(88, 'Data Management for NGOs', 'NGO Operations', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'Handling NGO data.', '9:20', '2025-07-01 08:56:01'),
(89, 'Community Needs Analysis', 'NGO Operations', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'Assessing community needs.', '10:45', '2025-07-01 08:56:01'),
(90, 'NGO Marketing Strategies', 'NGO Operations', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'Marketing NGO initiatives.', '11:15', '2025-07-01 08:56:01'),
(91, 'Event Logistics for NGOs', 'NGO Operations', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'Managing event logistics.', '10:10', '2025-07-01 08:56:01'),
(92, 'Donor Relationship Management', 'NGO Operations', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'Building donor relationships.', '9:50', '2025-07-01 08:56:01'),
(93, 'NGO Reporting Basics', 'NGO Operations', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'Creating effective reports.', '11:30', '2025-07-01 08:56:01'),
(94, 'Volunteer Training Programs', 'NGO Operations', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'Designing training programs.', '10:00', '2025-07-01 08:56:01'),
(95, 'NGO Partnerships', 'NGO Operations', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'Building partnerships.', '9:40', '2025-07-01 08:56:01'),
(96, 'Sustainability in NGOs', 'NGO Operations', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'Ensuring long-term impact.', '11:20', '2025-07-01 08:56:01'),
(97, 'Risk Management for NGOs', 'NGO Operations', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'Managing risks in operations.', '10:15', '2025-07-01 08:56:01'),
(98, 'Technology in NGOs', 'NGO Operations', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'Using tech for efficiency.', '9:30', '2025-07-01 08:56:01'),
(99, 'Advocacy Campaign Planning', 'NGO Operations', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'Planning advocacy campaigns.', '11:00', '2025-07-01 08:56:01'),
(100, 'NGO Impact Measurement', 'NGO Operations', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'Measuring program impact.', '10:50', '2025-07-01 08:56:01'),
(101, 'Introduction to Child Education', 'Child Education', 'https://www.youtube.com/embed/3z5jM8j8o4Q', 'UNICEF’s overview of early childhood education principles and practices.', '5:32', '2025-07-01 09:04:58'),
(102, 'Effective Teaching Techniques', 'Child Education', 'https://www.youtube.com/embed/6iP5tG1tM9g', 'Teach For India shares strategies for engaging young learners in classrooms.', '8:45', '2025-07-01 09:04:58'),
(103, 'Child Psychology Basics', 'Child Education', 'https://www.youtube.com/embed/4y0mV4T7N4o', 'Understanding child behavior and development from Save the Children.', '6:20', '2025-07-01 09:04:58'),
(104, 'Creating Safe Learning Spaces', 'Child Education', 'https://www.youtube.com/embed/2n7A7Ddg4jE', 'How to foster supportive classroom environments for children.', '7:10', '2025-07-01 09:04:58'),
(105, 'Volunteer Orientation', 'Volunteer Skills', 'https://www.youtube.com/embed/5gqYh7bN8Gk', 'UN Volunteers introduction to volunteering with NGOs.', '4:50', '2025-07-01 09:04:58'),
(106, 'Effective Communication', 'Volunteer Skills', 'https://www.youtube.com/embed/7OS6xN1K82w', 'Tips for building strong communication skills for volunteers.', '6:30', '2025-07-01 09:04:58'),
(107, 'Teamwork in NGOs', 'Volunteer Skills', 'https://www.youtube.com/embed/Q9y3zX5kZ5I', 'Collaborating effectively with volunteer teams.', '5:15', '2025-07-01 09:04:58'),
(108, 'Time Management for Volunteers', 'Volunteer Skills', 'https://www.youtube.com/embed/0Uwh5W8a6Hg', 'Balancing volunteer commitments with personal schedules.', '7:00', '2025-07-01 09:04:58'),
(109, 'Community Outreach Strategies', 'Community Engagement', 'https://www.youtube.com/embed/9k2W4qX9z3M', 'Red Cross strategies for engaging local communities.', '6:40', '2025-07-01 09:04:58'),
(110, 'Organizing Clean-Up Drives', 'Community Engagement', 'https://www.youtube.com/embed/4jK8h7zX9vU', 'How to plan and execute community clean-up initiatives.', '5:25', '2025-07-01 09:04:58'),
(111, 'Youth Engagement Programs', 'Community Engagement', 'https://www.youtube.com/embed/8v3h8kP2z0Q', 'UNICEF’s approach to involving youth in community activities.', '7:50', '2025-07-01 09:04:58'),
(112, 'Parent Workshops', 'Community Engagement', 'https://www.youtube.com/embed/6m4jN3gX8yI', 'Conducting workshops to engage parents in education.', '8:10', '2025-07-01 09:04:58'),
(113, 'Child Safety Protocols', 'Child Welfare', 'https://www.youtube.com/embed/1fX9pG8kZ3Q', 'Save the Children’s guide to ensuring child safety.', '6:15', '2025-07-01 09:04:58'),
(114, 'Child Nutrition Basics', 'Child Welfare', 'https://www.youtube.com/embed/3gH8m9W7y2U', 'UNICEF’s guide to promoting healthy diets for children.', '5:40', '2025-07-01 09:04:58'),
(115, 'Mental Health Support for Kids', 'Child Welfare', 'https://www.youtube.com/embed/7k9pH2xY8zQ', 'Addressing mental health needs in children.', '7:30', '2025-07-01 09:04:58'),
(116, 'Child Protection Policies', 'Child Welfare', 'https://www.youtube.com/embed/5yJ3kL9z0wE', 'Understanding child protection laws and policies.', '8:00', '2025-07-01 09:04:58'),
(117, 'NGO Fundraising Basics', 'NGO Operations', 'https://www.youtube.com/embed/2hK7pQ3z8vI', 'Smile Foundation’s guide to raising funds for NGOs.', '6:25', '2025-07-01 09:04:58'),
(118, 'Grant Writing for NGOs', 'NGO Operations', 'https://www.youtube.com/embed/9m2W5vY7z2Q', 'Crafting successful grant proposals for NGOs.', '7:45', '2025-07-01 09:04:58'),
(119, 'NGO Budget Management', 'NGO Operations', 'https://www.youtube.com/embed/4x8J9kP1z3W', 'Managing finances effectively for NGOs.', '6:50', '2025-07-01 09:04:58'),
(120, 'Program Evaluation', 'NGO Operations', 'https://www.youtube.com/embed/6v4N8hX9y0U', 'Evaluating NGO programs for impact and efficiency.', '8:20', '2025-07-01 09:04:58'),
(121, 'Introduction to Child Education', 'Child Education', 'https://www.youtube.com/embed/3z5jM8j8o4Q', 'UNICEF’s overview of early childhood education principles and practices.', '5:32', '2025-07-01 09:07:30'),
(122, 'Effective Teaching Techniques', 'Child Education', 'https://www.youtube.com/embed/6iP5tG1tM9g', 'Teach For India shares strategies for engaging young learners in classrooms.', '8:45', '2025-07-01 09:07:30'),
(123, 'Child Psychology Basics', 'Child Education', 'https://www.youtube.com/embed/ZX0m8s6oL3s', 'Understanding child behavior and development from Save the Children.', '6:20', '2025-07-01 09:07:30'),
(124, 'Creating Safe Learning Spaces', 'Child Education', 'https://www.youtube.com/embed/_TqO6Z7v9kM', 'How to foster supportive classroom environments for children.', '7:10', '2025-07-01 09:07:30'),
(125, 'Literacy Programs for Kids', 'Child Education', 'https://www.youtube.com/embed/4r4D8Z6Q7wE', 'Implementing reading initiatives for children in underserved communities.', '6:50', '2025-07-01 09:07:30'),
(126, 'Volunteer Orientation', 'Volunteer Skills', 'https://www.youtube.com/embed/5gqYh7bN8Gk', 'UN Volunteers introduction to volunteering with NGOs.', '4:50', '2025-07-01 09:07:30'),
(127, 'Effective Communication', 'Volunteer Skills', 'https://www.youtube.com/embed/7OS6xN1K82w', 'Tips for building strong communication skills for volunteers.', '6:30', '2025-07-01 09:07:30'),
(128, 'Teamwork in NGOs', 'Volunteer Skills', 'https://www.youtube.com/embed/Q9y3zX5kZ5I', 'Collaborating effectively with volunteer teams.', '5:15', '2025-07-01 09:07:30'),
(129, 'Time Management for Volunteers', 'Volunteer Skills', 'https://www.youtube.com/embed/0Uwh5W8a6Hg', 'Balancing volunteer commitments with personal schedules.', '7:00', '2025-07-01 09:07:30'),
(130, 'Fundraising Basics', 'Volunteer Skills', 'https://www.youtube.com/embed/2hK7pQ3z8vI', 'Smile Foundation’s guide to raising funds for NGOs.', '6:25', '2025-07-01 09:07:30'),
(131, 'Community Outreach Strategies', 'Community Engagement', 'https://www.youtube.com/embed/9k2W4qX9z3M', 'Red Cross strategies for engaging local communities.', '6:40', '2025-07-01 09:07:30'),
(132, 'Organizing Clean-Up Drives', 'Community Engagement', 'https://www.youtube.com/embed/4jK8h7zX9vU', 'How to plan and execute community clean-up initiatives.', '5:25', '2025-07-01 09:07:30'),
(133, 'Youth Engagement Programs', 'Community Engagement', 'https://www.youtube.com/embed/8v3h8kP2z0Q', 'UNICEF’s approach to involving youth in community activities.', '7:50', '2025-07-01 09:07:30'),
(134, 'Parent Workshops', 'Community Engagement', 'https://www.youtube.com/embed/6m4jN3gX8yI', 'Conducting workshops to engage parents in education.', '8:10', '2025-07-01 09:07:30'),
(135, 'Community Health Initiatives', 'Community Engagement', 'https://www.youtube.com/embed/3gH8m9W7y2U', 'Promoting health in communities through awareness and action.', '6:15', '2025-07-01 09:07:30'),
(136, 'Child Safety Protocols', 'Child Welfare', 'https://www.youtube.com/embed/1fX9pG8kZ3Q', 'Save the Children’s guide to ensuring child safety.', '6:15', '2025-07-01 09:07:30'),
(137, 'Child Nutrition Basics', 'Child Welfare', 'https://www.youtube.com/embed/7k9pH2xY8zQ', 'UNICEF’s guide to promoting healthy diets for children.', '5:40', '2025-07-01 09:07:30'),
(138, 'Mental Health Support for Kids', 'Child Welfare', 'https://www.youtube.com/embed/5yJ3kL9z0wE', 'Addressing mental health needs in children.', '7:30', '2025-07-01 09:07:30'),
(139, 'Child Protection Policies', 'Child Welfare', 'https://www.youtube.com/embed/2n7A7Ddg4jE', 'Understanding child protection laws and policies.', '8:00', '2025-07-01 09:07:30'),
(140, 'Trauma-Informed Care', 'Child Welfare', 'https://www.youtube.com/embed/4y0mV4T7N4o', 'Supporting children with trauma in educational settings.', '6:45', '2025-07-01 09:07:30'),
(141, 'NGO Fundraising Basics', 'NGO Operations', 'https://www.youtube.com/embed/2hK7pQ3z8vI', 'Smile Foundation’s guide to raising funds for NGOs.', '6:25', '2025-07-01 09:07:30'),
(142, 'Grant Writing for NGOs', 'NGO Operations', 'https://www.youtube.com/embed/9m2W5vY7z2Q', 'Crafting successful grant proposals for NGOs.', '7:45', '2025-07-01 09:07:30'),
(143, 'NGO Budget Management', 'NGO Operations', 'https://www.youtube.com/embed/4x8J9kP1z3W', 'Managing finances effectively for NGOs.', '6:50', '2025-07-01 09:07:30'),
(144, 'Program Evaluation', 'NGO Operations', 'https://www.youtube.com/embed/6v4N8hX9y0U', 'Evaluating NGO programs for impact and efficiency.', '8:20', '2025-07-01 09:07:30'),
(145, 'Volunteer Coordination', 'NGO Operations', 'https://www.youtube.com/embed/5gqYh7bN8Gk', 'Managing volunteer teams effectively for NGO success.', '5:30', '2025-07-01 09:07:30');

-- --------------------------------------------------------

--
-- Table structure for table `redemptions`
--

CREATE TABLE `redemptions` (
  `id` int(11) NOT NULL,
  `volunteer_id` int(11) NOT NULL,
  `reward_id` int(11) NOT NULL,
  `redeemed_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rewards`
--

CREATE TABLE `rewards` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `points_required` int(11) NOT NULL,
  `type` enum('merchandise','certificate','event') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rewards`
--

INSERT INTO `rewards` (`id`, `name`, `description`, `points_required`, `type`) VALUES
(1, 'NGO T-Shirt', 'Official Space ECE T-Shirt', 200, 'merchandise'),
(2, 'Volunteer Certificate', 'E-Certificate of Appreciation', 150, 'certificate'),
(3, 'Donor Event Access', 'Exclusive access to donor event', 300, 'event');

-- --------------------------------------------------------

--
-- Table structure for table `stories`
--

CREATE TABLE `stories` (
  `id` int(11) NOT NULL,
  `volunteer_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `stories`
--

INSERT INTO `stories` (`id`, `volunteer_id`, `title`, `content`, `created_at`) VALUES
(1, 8, 'my story', 'hello', '2025-07-01 07:38:25'),
(2, 8, 'my story', 'hello', '2025-07-01 07:38:30');

-- --------------------------------------------------------

--
-- Table structure for table `survey_submissions`
--

CREATE TABLE `survey_submissions` (
  `longitude` decimal(11,8) DEFAULT NULL,
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `feedback` text NOT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `submission_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `survey_submissions`
--

INSERT INTO `survey_submissions` (`longitude`, `id`, `name`, `email`, `feedback`, `latitude`, `submission_date`) VALUES
(77.02118400, 1, 'Akash Jindal', 'akashjindal512@gmail.com', 'xyz', 26.75179520, '2025-07-22 19:30:20'),
(77.02118400, 2, 'Akash Jindal', 'akashjindal512@gmail.com', 'xyz', 26.75179520, '2025-07-22 19:30:28');

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

CREATE TABLE `tasks` (
  `id` int(11) NOT NULL,
  `volunteer_id` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `required_skills` varchar(255) DEFAULT NULL,
  `type` varchar(100) DEFAULT NULL,
  `status` enum('open','assigned','completed') DEFAULT 'open',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tasks`
--

INSERT INTO `tasks` (`id`, `volunteer_id`, `title`, `description`, `required_skills`, `type`, `status`, `created_at`) VALUES
(1, NULL, 'Organize Community Event', 'Plan a local charity event', 'Event Planning', 'Fundraising and Outreach', 'open', '2025-07-01 07:15:49'),
(2, NULL, 'Tutor Children', 'Teach basic math to kids', 'Teaching', 'Education and Training', 'open', '2025-07-01 07:15:49'),
(3, NULL, 'Organize Community Event', 'Plan a local charity event', 'Event Planning', 'Fundraising and Outreach', 'open', '2025-07-01 07:29:58'),
(4, NULL, 'Tutor Children', 'Teach basic math to kids', 'Teaching', 'Education and Training', 'open', '2025-07-01 07:29:58'),
(5, NULL, 'Organize Community Event', 'Plan a local charity event', 'Event Planning', 'Fundraising and Outreach', 'open', '2025-07-01 07:30:21'),
(6, NULL, 'Tutor Children', 'Teach basic math to kids', 'Teaching', 'Education and Training', 'open', '2025-07-01 07:30:21'),
(7, NULL, 'Organize Community Event', 'Plan a local charity event', 'Event Planning', 'Fundraising and Outreach', 'open', '2025-07-01 07:30:34'),
(8, NULL, 'Tutor Children', 'Teach basic math to kids', 'Teaching', 'Education and Training', 'open', '2025-07-01 07:30:34'),
(9, 8, 'Prepare meeting agendas', 'important', 'knowledge', 'Administrative Support', 'open', '2025-07-01 09:26:06'),
(10, 8, 'Answer and route incoming calls', '', '', 'Administrative Support', 'open', '2025-07-01 10:15:59'),
(11, 8, 'Answer and route incoming calls', '', '', 'Administrative Support', 'open', '2025-07-01 10:22:38');

-- --------------------------------------------------------

--
-- Table structure for table `video_progress`
--

CREATE TABLE `video_progress` (
  `id` int(11) NOT NULL,
  `volunteer_id` int(11) NOT NULL,
  `video_id` int(11) NOT NULL,
  `watched` tinyint(1) DEFAULT 0,
  `watched_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `video_progress`
--

INSERT INTO `video_progress` (`id`, `volunteer_id`, `video_id`, `watched`, `watched_at`) VALUES
(1, 9, 8, 1, '2025-07-01 09:01:57'),
(2, 9, 8, 1, '2025-07-01 09:05:05'),
(3, 9, 8, 1, '2025-07-01 09:08:14'),
(4, 9, 8, 1, '2025-07-01 09:09:58'),
(5, 8, 8, 1, '2025-07-01 09:24:17');

-- --------------------------------------------------------

--
-- Table structure for table `volunteers`
--

CREATE TABLE `volunteers` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `skills` text DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `points` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `availability` varchar(255) DEFAULT NULL,
  `experience` text DEFAULT NULL,
  `profile_pic` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `volunteers`
--

INSERT INTO `volunteers` (`id`, `name`, `email`, `username`, `password`, `skills`, `location`, `points`, `created_at`, `availability`, `experience`, `profile_pic`) VALUES
(8, 'KUSH', 'test@example.com', 'KUSH', 'testpass', 'coding', 'India', 100, '2025-06-30 07:13:40', 'weekends', '1 year', NULL),
(9, 'Nupur', 'testuser@gmail.com', 'NUPUR', 'testpass1', 'CODING', 'Haryana', 0, '2025-06-30 07:15:49', '10 hours', 'no', NULL),
(14, 'Akash ', 'akashjindal512@gmail.com', 'Akash', 'Akash@12345', 'PHP', 'Rajasthan', 0, '2025-07-22 07:03:59', '1min/week', '10+ years', NULL),
(15, 'kaushik', 'hubvuvbeuib@gmail.com', 'kaushik', 'kaushik', 'no', 'hjsbdhdb', 0, '2025-07-22 13:31:53', 'hubdsub', 'yudbiuf', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `badges`
--
ALTER TABLE `badges`
  ADD PRIMARY KEY (`id`),
  ADD KEY `volunteer_id` (`volunteer_id`);

--
-- Indexes for table `certificates`
--
ALTER TABLE `certificates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `volunteer_id` (`volunteer_id`),
  ADD KEY `layout_id` (`layout_id`);

--
-- Indexes for table `certificate_layouts`
--
ALTER TABLE `certificate_layouts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contact_submissions`
--
ALTER TABLE `contact_submissions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `impact_portfolio`
--
ALTER TABLE `impact_portfolio`
  ADD PRIMARY KEY (`id`),
  ADD KEY `volunteer_id` (`volunteer_id`);

--
-- Indexes for table `learning_resources`
--
ALTER TABLE `learning_resources`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `redemptions`
--
ALTER TABLE `redemptions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `volunteer_id` (`volunteer_id`),
  ADD KEY `reward_id` (`reward_id`);

--
-- Indexes for table `rewards`
--
ALTER TABLE `rewards`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `stories`
--
ALTER TABLE `stories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `volunteer_id` (`volunteer_id`);

--
-- Indexes for table `survey_submissions`
--
ALTER TABLE `survey_submissions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `volunteer_id` (`volunteer_id`);

--
-- Indexes for table `video_progress`
--
ALTER TABLE `video_progress`
  ADD PRIMARY KEY (`id`),
  ADD KEY `volunteer_id` (`volunteer_id`),
  ADD KEY `video_id` (`video_id`);

--
-- Indexes for table `volunteers`
--
ALTER TABLE `volunteers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `badges`
--
ALTER TABLE `badges`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `certificates`
--
ALTER TABLE `certificates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `certificate_layouts`
--
ALTER TABLE `certificate_layouts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `contact_submissions`
--
ALTER TABLE `contact_submissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `impact_portfolio`
--
ALTER TABLE `impact_portfolio`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `learning_resources`
--
ALTER TABLE `learning_resources`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=146;

--
-- AUTO_INCREMENT for table `redemptions`
--
ALTER TABLE `redemptions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rewards`
--
ALTER TABLE `rewards`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `stories`
--
ALTER TABLE `stories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `survey_submissions`
--
ALTER TABLE `survey_submissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `video_progress`
--
ALTER TABLE `video_progress`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `volunteers`
--
ALTER TABLE `volunteers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `badges`
--
ALTER TABLE `badges`
  ADD CONSTRAINT `badges_ibfk_1` FOREIGN KEY (`volunteer_id`) REFERENCES `volunteers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `certificates`
--
ALTER TABLE `certificates`
  ADD CONSTRAINT `certificates_ibfk_1` FOREIGN KEY (`volunteer_id`) REFERENCES `volunteers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `certificates_ibfk_2` FOREIGN KEY (`layout_id`) REFERENCES `certificate_layouts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `impact_portfolio`
--
ALTER TABLE `impact_portfolio`
  ADD CONSTRAINT `impact_portfolio_ibfk_1` FOREIGN KEY (`volunteer_id`) REFERENCES `volunteers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `redemptions`
--
ALTER TABLE `redemptions`
  ADD CONSTRAINT `redemptions_ibfk_1` FOREIGN KEY (`volunteer_id`) REFERENCES `volunteers` (`id`),
  ADD CONSTRAINT `redemptions_ibfk_2` FOREIGN KEY (`reward_id`) REFERENCES `rewards` (`id`);

--
-- Constraints for table `stories`
--
ALTER TABLE `stories`
  ADD CONSTRAINT `stories_ibfk_1` FOREIGN KEY (`volunteer_id`) REFERENCES `volunteers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tasks`
--
ALTER TABLE `tasks`
  ADD CONSTRAINT `tasks_ibfk_1` FOREIGN KEY (`volunteer_id`) REFERENCES `volunteers` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `video_progress`
--
ALTER TABLE `video_progress`
  ADD CONSTRAINT `video_progress_ibfk_1` FOREIGN KEY (`volunteer_id`) REFERENCES `volunteers` (`id`),
  ADD CONSTRAINT `video_progress_ibfk_2` FOREIGN KEY (`video_id`) REFERENCES `learning_resources` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
