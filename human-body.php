<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Human Body & Skeleton - Meducare</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="assets/css/human-body.css">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <main class="container">
        <div class="body-header">
            <h1>Human Body & Skeleton</h1>
            <p>Interactive guide to human anatomy - click on parts to see details</p>
        </div>

        <!-- Human Body Section -->
        <div class="body-diagram-section">
            <h2>Human Body Organs</h2>
            <div class="diagram-container">
                <div class="body-image">
                    <svg viewBox="0 0 300 600" class="body-svg">
                        <!-- Head -->
                        <circle cx="150" cy="80" r="50" class="body-part" data-part="head" onclick="showBodyPart('head')"/>
                        <text x="150" y="40" text-anchor="middle" class="part-label">Head</text>
                        
                        <!-- Neck -->
                        <rect x="140" y="130" width="20" height="30" class="body-part" data-part="neck" onclick="showBodyPart('neck')"/>
                        <text x="150" y="120" text-anchor="middle" class="part-label">Neck</text>
                        
                        <!-- Shoulders -->
                        <rect x="100" y="160" width="40" height="30" class="body-part" data-part="shoulder-left" onclick="showBodyPart('shoulder')"/>
                        <rect x="160" y="160" width="40" height="30" class="body-part" data-part="shoulder-right" onclick="showBodyPart('shoulder')"/>
                        
                        <!-- Chest -->
                        <rect x="120" y="190" width="60" height="70" class="body-part" data-part="chest" onclick="showBodyPart('chest')"/>
                        
                        <!-- Heart -->
                        <path d="M150 220 L140 210 L150 200 L160 210 L150 220" fill="#ff6b6b" class="body-part" data-part="heart" onclick="showBodyPart('heart')"/>
                        
                        <!-- Lungs -->
                        <rect x="100" y="210" width="30" height="40" rx="5" class="body-part" data-part="lung-left" onclick="showBodyPart('lungs')"/>
                        <rect x="170" y="210" width="30" height="40" rx="5" class="body-part" data-part="lung-right" onclick="showBodyPart('lungs')"/>
                        
                        <!-- Stomach -->
                        <ellipse cx="150" cy="280" rx="25" ry="35" class="body-part" data-part="stomach" onclick="showBodyPart('stomach')"/>
                        
                        <!-- Liver -->
                        <path d="M180 260 L190 250 L200 270 L190 280 L180 260" fill="#8b4513" class="body-part" data-part="liver" onclick="showBodyPart('liver')"/>
                        
                        <!-- Intestines -->
                        <rect x="135" y="315" width="30" height="50" class="body-part" data-part="intestines" onclick="showBodyPart('intestines')"/>
                        
                        <!-- Arms -->
                        <rect x="60" y="190" width="30" height="120" rx="5" class="body-part" data-part="arm-left" onclick="showBodyPart('arm')"/>
                        <rect x="210" y="190" width="30" height="120" rx="5" class="body-part" data-part="arm-right" onclick="showBodyPart('arm')"/>
                        
                        <!-- Legs -->
                        <rect x="120" y="370" width="25" height="150" rx="5" class="body-part" data-part="leg-left" onclick="showBodyPart('leg')"/>
                        <rect x="155" y="370" width="25" height="150" rx="5" class="body-part" data-part="leg-right" onclick="showBodyPart('leg')"/>
                    </svg>
                </div>

                <div class="body-labels" id="bodyInfo">
                    <h3>Human Body Parts</h3>
                    <p>Click on any body part to see details</p>
                    <div class="organs-list">
                        <div class="organ-item" onclick="showBodyPart('brain')">
                            <span class="organ-dot" style="background: #6c5ce7;"></span>
                            <span>Brain</span>
                        </div>
                        <div class="organ-item" onclick="showBodyPart('heart')">
                            <span class="organ-dot" style="background: #ff6b6b;"></span>
                            <span>Heart</span>
                        </div>
                        <div class="organ-item" onclick="showBodyPart('lungs')">
                            <span class="organ-dot" style="background: #4ecdc4;"></span>
                            <span>Lungs</span>
                        </div>
                        <div class="organ-item" onclick="showBodyPart('stomach')">
                            <span class="organ-dot" style="background: #ffd93d;"></span>
                            <span>Stomach</span>
                        </div>
                        <div class="organ-item" onclick="showBodyPart('liver')">
                            <span class="organ-dot" style="background: #8b4513;"></span>
                            <span>Liver</span>
                        </div>
                        <div class="organ-item" onclick="showBodyPart('kidneys')">
                            <span class="organ-dot" style="background: #a8e6cf;"></span>
                            <span>Kidneys</span>
                        </div>
                        <div class="organ-item" onclick="showBodyPart('intestines')">
                            <span class="organ-dot" style="background: #ff8c94;"></span>
                            <span>Intestines</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Skeleton Section -->
        <div class="skeleton-section">
            <h2>Human Skeleton</h2>
            <div class="skeleton-diagram">
                <div class="skeleton-image">
                    <svg viewBox="0 0 300 600" class="skeleton-svg">
                        <!-- Skull -->
                        <circle cx="150" cy="70" r="30" class="bone" data-bone="skull" onclick="showBone('skull')"/>
                        <circle cx="140" cy="65" r="5" class="bone" data-bone="eye-socket"/>
                        <circle cx="160" cy="65" r="5" class="bone" data-bone="eye-socket"/>
                        
                        <!-- Spine -->
                        <rect x="145" y="100" width="10" height="250" class="bone" data-bone="spine" onclick="showBone('spine')"/>
                        
                        <!-- Rib Cage -->
                        <path d="M120 130 Q150 140 180 130" class="bone" data-bone="ribs" onclick="showBone('ribs')"/>
                        <path d="M120 150 Q150 160 180 150" class="bone" data-bone="ribs"/>
                        <path d="M120 170 Q150 180 180 170" class="bone" data-bone="ribs"/>
                        
                        <!-- Clavicle -->
                        <line x1="120" y1="120" x2="180" y2="120" class="bone" data-bone="clavicle" onclick="showBone('clavicle')"/>
                        
                        <!-- Scapula -->
                        <path d="M100 140 L110 130 L120 140" class="bone" data-bone="scapula" onclick="showBone('scapula')"/>
                        <path d="M180 140 L190 130 L200 140" class="bone" data-bone="scapula"/>
                        
                        <!-- Humerus -->
                        <rect x="90" y="180" width="15" height="100" class="bone" data-bone="humerus" onclick="showBone('humerus')"/>
                        <rect x="195" y="180" width="15" height="100" class="bone" data-bone="humerus"/>
                        
                        <!-- Radius/Ulna -->
                        <rect x="80" y="280" width="10" height="80" class="bone" data-bone="radius" onclick="showBone('radius')"/>
                        <rect x="105" y="280" width="10" height="80" class="bone" data-bone="ulna"/>
                        <rect x="185" y="280" width="10" height="80" class="bone" data-bone="radius"/>
                        <rect x="210" y="280" width="10" height="80" class="bone" data-bone="ulna"/>
                        
                        <!-- Pelvis -->
                        <path d="M130 350 L150 330 L170 350" class="bone" data-bone="pelvis" onclick="showBone('pelvis')"/>
                        
                        <!-- Femur -->
                        <rect x="135" y="360" width="12" height="120" class="bone" data-bone="femur" onclick="showBone('femur')"/>
                        <rect x="153" y="360" width="12" height="120" class="bone" data-bone="femur"/>
                        
                        <!-- Patella -->
                        <circle cx="150" cy="480" r="8" class="bone" data-bone="patella" onclick="showBone('patella')"/>
                        
                        <!-- Tibia/Fibula -->
                        <rect x="135" y="490" width="8" height="80" class="bone" data-bone="tibia" onclick="showBone('tibia')"/>
                        <rect x="157" y="490" width="8" height="80" class="bone" data-bone="tibia"/>
                        <rect x="125" y="490" width="8" height="70" class="bone" data-bone="fibula"/>
                        <rect x="167" y="490" width="8" height="70" class="bone" data-bone="fibula"/>
                    </svg>
                </div>

                <div class="bone-info" id="boneInfo">
                    <h3>Skeleton Parts</h3>
                    <p>Click on any bone to learn more</p>
                    <div class="bone-list">
                        <div class="bone-category">
                            <h4>Skull</h4>
                            <ul>
                                <li onclick="showBone('skull')">Skull (Cranium)</li>
                                <li onclick="showBone('mandible')">Mandible (Jaw)</li>
                                <li onclick="showBone('maxilla')">Maxilla</li>
                            </ul>
                        </div>
                        <div class="bone-category">
                            <h4>Spine</h4>
                            <ul>
                                <li onclick="showBone('cervical')">Cervical Vertebrae (7)</li>
                                <li onclick="showBone('thoracic')">Thoracic Vertebrae (12)</li>
                                <li onclick="showBone('lumbar')">Lumbar Vertebrae (5)</li>
                                <li onclick="showBone('sacrum')">Sacrum</li>
                                <li onclick="showBone('coccyx')">Coccyx (Tailbone)</li>
                            </ul>
                        </div>
                        <div class="bone-category">
                            <h4>Rib Cage</h4>
                            <ul>
                                <li onclick="showBone('sternum')">Sternum</li>
                                <li onclick="showBone('ribs')">Ribs (12 pairs)</li>
                            </ul>
                        </div>
                        <div class="bone-category">
                            <h4>Arm Bones</h4>
                            <ul>
                                <li onclick="showBone('clavicle')">Clavicle (Collarbone)</li>
                                <li onclick="showBone('scapula')">Scapula (Shoulder Blade)</li>
                                <li onclick="showBone('humerus')">Humerus (Upper Arm)</li>
                                <li onclick="showBone('radius')">Radius</li>
                                <li onclick="showBone('ulna')">Ulna</li>
                                <li onclick="showBone('carpals')">Carpals (Wrist)</li>
                                <li onclick="showBone('metacarpals')">Metacarpals (Hand)</li>
                                <li onclick="showBone('phalanges')">Phalanges (Fingers)</li>
                            </ul>
                        </div>
                        <div class="bone-category">
                            <h4>Leg Bones</h4>
                            <ul>
                                <li onclick="showBone('pelvis')">Pelvis (Hip Bone)</li>
                                <li onclick="showBone('femur')">Femur (Thigh Bone)</li>
                                <li onclick="showBone('patella')">Patella (Kneecap)</li>
                                <li onclick="showBone('tibia')">Tibia (Shin Bone)</li>
                                <li onclick="showBone('fibula')">Fibula</li>
                                <li onclick="showBone('tarsals')">Tarsals (Ankle)</li>
                                <li onclick="showBone('metatarsals')">Metatarsals (Foot)</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Educational Info Section -->
        <div class="educational-info">
            <div class="info-card">
                <h3>Did You Know?</h3>
                <ul>
                    <li>The human body has 206 bones</li>
                    <li>The smallest bone is in your ear (stapes)</li>
                    <li>The longest bone is your femur (thigh bone)</li>
                    <li>Babies are born with about 300 bones</li>
                    <li>Your nose and ears never stop growing</li>
                </ul>
            </div>
            <div class="info-card">
                <h3>Organ Facts</h3>
                <ul>
                    <li>Your heart beats about 100,000 times daily</li>
                    <li>Lungs contain 300 million tiny air sacs</li>
                    <li>The liver can regenerate itself</li>
                    <li>Stomach acid can dissolve metal</li>
                    <li>Your brain uses 20% of your oxygen</li>
                </ul>
            </div>
        </div>
    </main>

    <!-- Body Part Info Modal -->
    <div class="modal" id="bodyPartModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="bodyPartTitle">Body Part Information</h2>
                <button class="close-modal" onclick="closeModal('bodyPartModal')">&times;</button>
            </div>
            <div id="bodyPartContent" class="body-part-content"></div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script>
    const bodyPartInfo = {
        head: {
            name: 'Head',
            description: 'The head contains the brain, eyes, ears, nose, and mouth. It protects the brain and houses sensory organs.',
            facts: [
                'The average adult head weighs about 5 kg',
                'There are 22 bones in the skull',
                'The brain uses 20% of the body\'s energy'
            ]
        },
        brain: {
            name: 'Brain',
            description: 'The brain is the control center of the body, responsible for thoughts, memory, emotions, and coordinating all bodily functions.',
            facts: [
                'Contains about 86 billion neurons',
                'Weighs about 1.4 kg in adults',
                'Generates enough electricity to power a light bulb'
            ]
        },
        heart: {
            name: 'Heart',
            description: 'The heart is a muscular organ that pumps blood throughout the body, delivering oxygen and nutrients to tissues.',
            facts: [
                'Beats about 100,000 times per day',
                'Pumps about 7,500 liters of blood daily',
                'Can continue beating outside the body'
            ]
        },
        lungs: {
            name: 'Lungs',
            description: 'The lungs are responsible for gas exchange, taking in oxygen and releasing carbon dioxide.',
            facts: [
                'Contain about 300 million alveoli',
                'Surface area equal to a tennis court',
                'Process about 11,000 liters of air daily'
            ]
        },
        stomach: {
            name: 'Stomach',
            description: 'The stomach breaks down food using acid and enzymes, beginning the digestive process.',
            facts: [
                'Produces gastric acid strong enough to dissolve metal',
                'Can stretch to hold up to 4 liters of food',
                'Renews its lining every 3-4 days'
            ]
        },
        liver: {
            name: 'Liver',
            description: 'The liver processes nutrients, filters toxins, and produces bile for digestion.',
            facts: [
                'Can regenerate itself',
                'Performs over 500 functions',
                'Weighs about 1.5 kg in adults'
            ]
        },
        kidneys: {
            name: 'Kidneys',
            description: 'The kidneys filter waste from blood, regulate blood pressure, and maintain fluid balance.',
            facts: [
                'Filter about 200 liters of blood daily',
                'Produce 1-2 liters of urine daily',
                'Can function with only one kidney'
            ]
        },
        intestines: {
            name: 'Intestines',
            description: 'The intestines absorb nutrients and water from food and eliminate waste.',
            facts: [
                'Small intestine is about 6 meters long',
                'Large intestine is about 1.5 meters long',
                'Surface area of small intestine equals a tennis court'
            ]
        }
    };

    const boneInfo = {
        skull: {
            name: 'Skull (Cranium)',
            description: 'The skull protects the brain and supports the structures of the face.',
            facts: ['Made up of 22 bones', '8 cranial bones, 14 facial bones', 'Only the mandible (jaw) moves']
        },
        spine: {
            name: 'Spine (Vertebral Column)',
            description: 'The spine protects the spinal cord and supports the body\'s weight.',
            facts: ['33 vertebrae in total', 'Divided into 5 regions', 'Allows flexibility and movement']
        },
        ribs: {
            name: 'Rib Cage',
            description: 'The rib cage protects the heart and lungs and assists in breathing.',
            facts: ['12 pairs of ribs', '7 true ribs, 5 false ribs', 'Men and women have same number of ribs']
        },
        clavicle: {
            name: 'Clavicle (Collarbone)',
            description: 'The clavicle connects the arm to the body and stabilizes shoulder movement.',
            facts: ['One of the most commonly broken bones', 'First bone to begin ossifying', 'Helps protect nerves and blood vessels']
        },
        scapula: {
            name: 'Scapula (Shoulder Blade)',
            description: 'The scapula connects the humerus with the clavicle and allows shoulder movement.',
            facts: ['Triangular-shaped bone', 'Has 17 muscle attachments', 'Helps in arm rotation']
        },
        humerus: {
            name: 'Humerus',
            description: 'The humerus is the long bone of the upper arm.',
            facts: ['Largest bone in the upper limb', 'Connects shoulder to elbow', 'Helps in arm rotation']
        },
        radius: {
            name: 'Radius',
            description: 'The radius is one of two bones in the forearm, located on the thumb side.',
            facts: ['Allows forearm rotation', 'Shorter than the ulna', 'Important for wrist movement']
        },
        ulna: {
            name: 'Ulna',
            description: 'The ulna is the larger of the two forearm bones, located on the pinky side.',
            facts: ['Forms the elbow joint', 'Longer than the radius', 'Stabilizes forearm']
        },
        pelvis: {
            name: 'Pelvis (Hip Bone)',
            description: 'The pelvis supports the upper body weight and protects pelvic organs.',
            facts: ['Made of 3 fused bones', 'Different shape in males and females', 'Supports digestion and reproduction']
        },
        femur: {
            name: 'Femur (Thigh Bone)',
            description: 'The femur is the longest and strongest bone in the human body.',
            facts: ['About 1/4 of your height', 'Can support 30 times body weight', 'Takes years to heal if broken']
        },
        patella: {
            name: 'Patella (Kneecap)',
            description: 'The patella protects the knee joint and improves leg extension.',
            facts: ['Largest sesamoid bone', 'Embedded in tendon', 'Helps with knee leverage']
        },
        tibia: {
            name: 'Tibia (Shin Bone)',
            description: 'The tibia bears most of the body weight and connects knee to ankle.',
            facts: ['Second largest bone in body', 'Supports body weight', 'Common site for stress fractures']
        },
        fibula: {
            name: 'Fibula',
            description: 'The fibula stabilizes the ankle and supports leg muscles.',
            facts: ['Does not bear body weight', 'Thinner than tibia', 'Important for ankle stability']
        }
    };

    function showBodyPart(part) {
        const info = bodyPartInfo[part];
        if (!info) return;
        
        document.getElementById('bodyPartTitle').textContent = info.name;
        
        let factsHtml = '';
        if (info.facts) {
            factsHtml = '<h4>Interesting Facts:</h4><ul>';
            info.facts.forEach(fact => {
                factsHtml += `<li>${fact}</li>`;
            });
            factsHtml += '</ul>';
        }
        
        document.getElementById('bodyPartContent').innerHTML = `
            <p class="part-description">${info.description}</p>
            ${factsHtml}
            <div class="part-image">
                <i class="fas fa-${part === 'heart' ? 'heart' : 
                                   part === 'brain' ? 'brain' : 
                                   part === 'lungs' ? 'lungs' : 
                                   part === 'stomach' ? 'stomach' : 
                                   part === 'liver' ? 'liver' : 
                                   part === 'kidneys' ? 'kidneys' : 
                                   part === 'intestines' ? 'intestines' : 
                                   'user-md'}"></i>
            </div>
        `;
        
        openModal('bodyPartModal');
    }

    function showBone(bone) {
        const info = boneInfo[bone];
        if (!info) return;
        
        document.getElementById('bodyPartTitle').textContent = info.name;
        
        let factsHtml = '';
        if (info.facts) {
            factsHtml = '<h4>Interesting Facts:</h4><ul>';
            info.facts.forEach(fact => {
                factsHtml += `<li>${fact}</li>`;
            });
            factsHtml += '</ul>';
        }
        
        document.getElementById('bodyPartContent').innerHTML = `
            <p class="part-description">${info.description}</p>
            ${factsHtml}
            <div class="part-image">
                <i class="fas fa-bone"></i>
            </div>
        `;
        
        openModal('bodyPartModal');
    }
    </script>

    <script src="assets/js/main.js"></script>
</body>
</html>